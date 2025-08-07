<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\CreditPackage;
use Exception;

class InstallationController extends Controller
{
    public function index()
    {
        // Check if installation is already completed
        if (env('INSTALLATION_COMPLETED', false)) {
            return redirect('/')->with('error', 'Installation already completed.');
        }

        return view('installation.welcome');
    }

    public function requirements()
    {
        $requirements = [
            'PHP >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'BCMath Extension' => extension_loaded('bcmath'),
            'Ctype Extension' => extension_loaded('ctype'),
            'Curl Extension' => extension_loaded('curl'),
            'GD Extension' => extension_loaded('gd'),
            'JSON Extension' => extension_loaded('json'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'MySQLi Extension' => extension_loaded('mysqli'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('pdo'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'XML Extension' => extension_loaded('xml'),
            'Zip Extension' => extension_loaded('zip'),
        ];

        $permissions = [
            'storage/app/' => is_writable(storage_path('app')),
            'storage/framework/' => is_writable(storage_path('framework')),
            'storage/logs/' => is_writable(storage_path('logs')),
            'bootstrap/cache/' => is_writable(base_path('bootstrap/cache')),
        ];

        return view('installation.requirements', compact('requirements', 'permissions'));
    }

    public function database()
    {
        return view('installation.database');
    }

    public function testDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $connection = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}",
                $request->db_username,
                $request->db_password
            );
            
            return response()->json(['success' => true, 'message' => 'Database connection successful!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function saveDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $this->updateEnvFile([
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_database,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => $request->db_password,
            ]);

            return redirect()->route('installation.admin');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function admin()
    {
        return view('installation.admin');
    }

    public function saveAdmin(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
        ]);

        try {
            $this->updateEnvFile([
                'APP_NAME' => '"' . $request->app_name . '"',
                'APP_URL' => $request->app_url,
                'ADMIN_EMAIL' => $request->admin_email,
                'ADMIN_PASSWORD' => $request->admin_password,
            ]);

            session([
                'installation_admin' => [
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'password' => $request->admin_password,
                ]
            ]);

            return redirect()->route('installation.configuration');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function configuration()
    {
        return view('installation.configuration');
    }

    public function saveConfiguration(Request $request)
    {
        $request->validate([
            'openai_api_key' => 'nullable|string',
            'stripe_key' => 'nullable|string',
            'stripe_secret' => 'nullable|string',
            'google_client_id' => 'nullable|string',
            'google_client_secret' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
        ]);

        try {
            $envUpdates = [];
            
            if ($request->openai_api_key) {
                $envUpdates['OPENAI_API_KEY'] = $request->openai_api_key;
            }
            
            if ($request->stripe_key && $request->stripe_secret) {
                $envUpdates['STRIPE_KEY'] = $request->stripe_key;
                $envUpdates['STRIPE_SECRET'] = $request->stripe_secret;
            }
            
            if ($request->google_client_id && $request->google_client_secret) {
                $envUpdates['GOOGLE_CLIENT_ID'] = $request->google_client_id;
                $envUpdates['GOOGLE_CLIENT_SECRET'] = $request->google_client_secret;
            }
            
            if ($request->mail_host) {
                $envUpdates['MAIL_MAILER'] = 'smtp';
                $envUpdates['MAIL_HOST'] = $request->mail_host;
                $envUpdates['MAIL_PORT'] = $request->mail_port ?? 587;
                $envUpdates['MAIL_USERNAME'] = $request->mail_username;
                $envUpdates['MAIL_PASSWORD'] = $request->mail_password;
                $envUpdates['MAIL_FROM_ADDRESS'] = $request->mail_from_address;
            }

            $this->updateEnvFile($envUpdates);

            return redirect()->route('installation.finalize');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function finalize()
    {
        return view('installation.finalize');
    }

    public function install()
    {
        try {
            // Generate application key
            Artisan::call('key:generate', ['--force' => true]);
            
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            
            // Create admin user
            $adminData = session('installation_admin');
            if ($adminData) {
                User::create([
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'password' => Hash::make($adminData['password']),
                    'is_admin' => true,
                    'credits' => 1000, // Give admin some initial credits
                    'email_verified_at' => now(),
                ]);
            }
            
            // Create default credit packages
            $this->createDefaultCreditPackages();
            
            // Mark installation as completed
            $this->updateEnvFile(['INSTALLATION_COMPLETED' => 'true']);
            
            // Clear caches
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully!',
                'redirect' => route('login')
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage()
            ]);
        }
    }

    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            File::copy(base_path('.env.example'), $envFile);
        }
        
        $envContent = File::get($envFile);
        
        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }
        
        File::put($envFile, $envContent);
    }

    private function createDefaultCreditPackages()
    {
        $packages = [
            [
                'name' => 'Starter Pack',
                'slug' => 'starter-pack',
                'description' => 'Perfect for trying out our AI agents',
                'credits' => 100,
                'price' => 9.99,
                'bonus_credits' => 0,
                'is_popular' => false,
                'sort_order' => 1,
                'features' => ['100 AI Messages', 'Basic Support']
            ],
            [
                'name' => 'Popular Pack',
                'slug' => 'popular-pack',
                'description' => 'Most popular choice for regular users',
                'credits' => 500,
                'price' => 39.99,
                'bonus_credits' => 50,
                'is_popular' => true,
                'sort_order' => 2,
                'features' => ['500 AI Messages', '+50 Bonus Credits', 'Priority Support']
            ],
            [
                'name' => 'Pro Pack',
                'slug' => 'pro-pack',
                'description' => 'For power users and businesses',
                'credits' => 1500,
                'price' => 99.99,
                'bonus_credits' => 200,
                'is_popular' => false,
                'sort_order' => 3,
                'features' => ['1500 AI Messages', '+200 Bonus Credits', 'Premium Support', 'Custom AI Agents']
            ]
        ];

        foreach ($packages as $package) {
            CreditPackage::create($package);
        }
    }
}
