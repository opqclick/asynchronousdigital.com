<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'Android Development',
                'icon' => '<i class="fab fa-android"></i>',
                'short_description' => 'Native Android applications with exceptional performance and user experience.',
                'full_description' => 'We specialize in building high-performance native Android applications using Kotlin and Java. Our team follows Material Design guidelines to create intuitive, beautiful interfaces that users love. From concept to Play Store deployment, we handle every aspect of Android app development.',
                'pricing_model' => 'custom',
                'base_price' => 10000.00,
                'price_display' => 'Starting at $10,000',
                'features' => [
                    'Native Kotlin/Java development',
                    'Material Design implementation',
                    'Google Play Store deployment',
                    'Performance optimization',
                    'Offline functionality',
                    'Push notifications',
                ],
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Flutter Development',
                'icon' => '<i class="fas fa-mobile-alt"></i>',
                'short_description' => 'Cross-platform mobile apps for iOS and Android from a single codebase.',
                'full_description' => 'Build beautiful, natively compiled applications for mobile, web, and desktop from a single codebase using Flutter. Our Flutter experts create stunning UIs with smooth animations and excellent performance on both iOS and Android platforms.',
                'pricing_model' => 'custom',
                'base_price' => 12000.00,
                'price_display' => 'Starting at $12,000',
                'features' => [
                    'Single codebase for both platforms',
                    'Beautiful, customizable UI',
                    'Fast development & hot reload',
                    'Native performance',
                    'Rich ecosystem of packages',
                    'Cross-platform consistency',
                ],
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Web Applications',
                'icon' => '<i class="fas fa-code"></i>',
                'short_description' => 'Powerful, scalable web applications with modern frameworks.',
                'full_description' => 'We develop robust web applications using modern frameworks like Laravel, React, and Vue.js. Our solutions are scalable, secure, and optimized for performance. Whether you need a complex SaaS platform or a business management system, we deliver excellence.',
                'pricing_model' => 'custom',
                'base_price' => 15000.00,
                'price_display' => 'Starting at $15,000',
                'features' => [
                    'React, Vue, Laravel development',
                    'RESTful API development',
                    'Database design & optimization',
                    'Real-time features',
                    'Third-party integrations',
                    'Scalable architecture',
                ],
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Website Creation',
                'icon' => '<i class="fas fa-globe"></i>',
                'short_description' => 'Responsive, high-performing websites tailored to your needs.',
                'full_description' => 'From corporate websites to e-commerce platforms, we create stunning, responsive websites that convert visitors into customers. Our websites are SEO-optimized, fast-loading, and built with the latest web technologies.',
                'pricing_model' => 'fixed',
                'base_price' => 5000.00,
                'price_display' => 'Starting at $5,000',
                'features' => [
                    'Responsive design',
                    'SEO optimization',
                    'Content management systems',
                    'Fast loading speeds',
                    'Mobile-first approach',
                    'Analytics integration',
                ],
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'DevOps',
                'icon' => '<i class="fas fa-server"></i>',
                'short_description' => 'Continuous integration and delivery pipelines for streamlined development.',
                'full_description' => 'Streamline your development workflow with our DevOps expertise. We implement CI/CD pipelines, manage cloud infrastructure, and ensure your applications are deployed reliably and efficiently. From Docker containerization to Kubernetes orchestration, we have you covered.',
                'pricing_model' => 'hourly',
                'base_price' => 150.00,
                'price_display' => '$150/hour',
                'features' => [
                    'CI/CD pipeline setup',
                    'Cloud infrastructure management',
                    'Docker & Kubernetes',
                    'Monitoring & logging',
                    'Infrastructure as Code',
                    'Security best practices',
                ],
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Design Support',
                'icon' => '<i class="fas fa-pen-nib"></i>',
                'short_description' => 'Comprehensive UI/UX design and branding solutions.',
                'full_description' => 'Great design is at the heart of every successful digital product. Our design team creates intuitive user experiences and beautiful interfaces that align with your brand identity. From wireframes to high-fidelity prototypes, we bring your vision to life.',
                'pricing_model' => 'custom',
                'base_price' => 3000.00,
                'price_display' => 'Starting at $3,000',
                'features' => [
                    'UI/UX design',
                    'Brand identity development',
                    'Prototyping & wireframing',
                    'User research & testing',
                    'Design systems',
                    'Responsive layouts',
                ],
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
