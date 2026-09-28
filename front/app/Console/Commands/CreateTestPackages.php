<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\Product;
use App\Models\PackageProduct;

class CreateTestPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packages:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test packages for development';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Creating test packages...');

        // Vérifier s'il y a des packages existants
        $existingPackages = Package::count();
        if ($existingPackages > 0) {
            $this->warn("Found {$existingPackages} existing packages. Skipping creation.");
            return 0;
        }

        // Créer des packages de test
        $packages = [
            [
                'title' => 'Complete Web Development Package',
                'image' => 'web-dev-package.jpg',
                'description' => 'Master web development with our comprehensive package including HTML, CSS, JavaScript, React, and Node.js courses.',
                'is_active' => true,
            ],
            [
                'title' => 'Data Science Mastery Package',
                'image' => 'data-science-package.jpg',
                'description' => 'Learn data science from scratch with Python, Machine Learning, Statistics, and Data Visualization courses.',
                'is_active' => true,
            ],
            [
                'title' => 'Mobile App Development Package',
                'image' => 'mobile-dev-package.jpg',
                'description' => 'Build mobile applications for iOS and Android with React Native, Flutter, and native development courses.',
                'is_active' => true,
            ],
            [
                'title' => 'Digital Marketing Package',
                'image' => 'digital-marketing-package.jpg',
                'description' => 'Master digital marketing with SEO, Social Media Marketing, Google Ads, and Analytics courses.',
                'is_active' => true,
            ],
            [
                'title' => 'Cybersecurity Package',
                'image' => 'cybersecurity-package.jpg',
                'description' => 'Learn cybersecurity fundamentals, ethical hacking, network security, and penetration testing.',
                'is_active' => true,
            ],
        ];

        foreach ($packages as $packageData) {
            $package = Package::create($packageData);
            $this->info("Created package: {$package->title}");
            
            // Ajouter quelques produits au package (si des produits existent)
            $products = Product::take(3)->get();
            if ($products->count() > 0) {
                foreach ($products as $index => $product) {
                    PackageProduct::create([
                        'package_id' => $package->id,
                        'product_id' => $product->id,
                        'valeur_reduction' => rand(10, 30), // Réduction entre 10% et 30%
                        'is_active' => true,
                    ]);
                }
                $this->info("  Added {$products->count()} products to package");
            } else {
                $this->warn("  No products found to add to package");
            }
        }

        $this->info('Test packages created successfully!');
        return 0;
    }
}
