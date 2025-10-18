<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Research;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IslamicFinanceContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Islamic Finance categories...');
        $this->createCategories();
        
        $this->command->info('Creating Islamic Finance tags...');
        $this->createTags();
        
        $this->command->info('Creating Islamic Finance pages...');
        $this->createPages();
        
        $this->command->info('Creating Islamic Finance publications...');
        $this->createPublications();
        
        $this->command->info('Islamic Finance content created successfully!');
    }

    private function createCategories(): void
    {
        $categories = [
            [
                'name' => 'Shariah Advisory',
                'slug' => 'shariah-advisory',
                'description' => 'Comprehensive Islamic finance advisory and consultancy services',
            ],
            [
                'name' => 'Sukuk & Structuring',
                'slug' => 'sukuk-structuring',
                'description' => 'Islamic bond structuring and financial instruments',
            ],
            [
                'name' => 'Halal Certification',
                'slug' => 'halal-certification',
                'description' => 'Halal product and business certification services',
            ],
            [
                'name' => 'Audit & Compliance',
                'slug' => 'audit-compliance',
                'description' => 'Shariah audit and regulatory compliance services',
            ],
            [
                'name' => 'Training & Education',
                'slug' => 'training-education',
                'description' => 'Islamic finance training programs and workshops',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }

    private function createTags(): void
    {
        $tags = [
            'Islamic Banking',
            'Takaful',
            'Sukuk',
            'Shariah Compliance',
            'Halal Business',
            'Zakat',
            'Islamic Finance',
            'Fintech',
            'Crypto & Blockchain',
            'Investment Screening',
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tag)],
                [
                    'name' => $tag,
                    'slug' => Str::slug($tag),
                ]
            );
        }
    }

    private function createPages(): void
    {
        $pages = [
            [
                'title' => 'About HEAC',
                'slug' => 'about',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'Your Trusted Partner in Islamic Finance',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'HEAC was established to promote a Shariah-compliant global economy by providing innovative ethical financial solutions. We are dedicated to advancing the halal economy through comprehensive advisory services, expert guidance, and unwavering commitment to Islamic principles.',
                    ],
                    [
                        'type' => 'heading',
                        'content' => 'Our Mission',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'To encourage ethical principles in Islamic economic activity through tailored Shariah-compliant solutions that meet the highest standards of integrity and excellence.',
                    ],
                    [
                        'type' => 'heading',
                        'content' => 'Our Vision',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'To establish a robust halal economy free from riba-based constraints, where businesses and individuals can thrive while maintaining complete adherence to Islamic principles.',
                    ],
                ],
                'excerpt' => 'Learn about HEAC\'s mission to advance the halal economy globally.',
                'meta_title' => 'About HEAC - Islamic Finance Solutions',
                'meta_description' => 'HEAC provides comprehensive Shariah-compliant solutions for the global halal economy.',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Our Services',
                'slug' => 'services',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'Comprehensive Islamic Finance Solutions',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'HEAC offers a full spectrum of Shariah-compliant services designed to meet the diverse needs of our global clientele.',
                    ],
                    [
                        'type' => 'heading',
                        'content' => 'Shariah Advisory & Consultancy',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'Comprehensive Islamic finance advisory services tailored to your business needs, including product design, regulatory reform advice, and corporate structuring.',
                    ],
                    [
                        'type' => 'heading',
                        'content' => 'Shariah Audit & Compliance',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'Regular internal and external Shariah audits for banks, Takaful, and investment firms, ensuring all transactions adhere to Islamic law.',
                    ],
                    [
                        'type' => 'heading',
                        'content' => 'Sukuk Structuring',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'Expert design of complex Islamic financial instruments including Sukuk, Islamic funds, and Takaful models.',
                    ],
                    [
                        'type' => 'heading',
                        'content' => 'Halal Certification',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'Comprehensive halal certification for products and business operations across food, cosmetics, travel, and other sectors.',
                    ],
                ],
                'excerpt' => 'Explore our comprehensive range of Shariah-compliant services.',
                'meta_title' => 'Services - HEAC Islamic Finance Solutions',
                'meta_description' => 'Comprehensive Shariah advisory, audit, certification, and structuring services.',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Training & Events',
                'slug' => 'training',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'Islamic Finance Training Programs',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'HEAC designs custom training programs and workshops for professionals seeking to enhance their knowledge of Islamic finance and Shariah compliance.',
                    ],
                    [
                        'type' => 'list',
                        'content' => [
                            'Islamic Banking Fundamentals',
                            'Advanced Shariah Audit',
                            'Sukuk Structuring Workshop',
                            'Halal Business Management',
                            'Fintech & Shariah Compliance',
                        ],
                    ],
                ],
                'excerpt' => 'Professional training programs in Islamic finance and Shariah compliance.',
                'meta_title' => 'Training & Events - HEAC',
                'meta_description' => 'Custom training programs and workshops on Islamic finance principles.',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Our Team & Scholars',
                'slug' => 'team',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'Meet Our Expert Team',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'Our team comprises recognized Shariah scholars, Islamic finance experts, and industry professionals with decades of combined experience serving clients worldwide.',
                    ],
                    [
                        'type' => 'heading',
                        'content' => 'Our Approach',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'We combine deep Islamic scholarship with modern financial expertise to deliver customized solutions that meet both Shariah requirements and business objectives.',
                    ],
                ],
                'excerpt' => 'Meet our team of Shariah scholars and Islamic finance experts.',
                'meta_title' => 'Team & Scholars - HEAC',
                'meta_description' => 'Our team of recognized Shariah scholars and Islamic finance professionals.',
                'status' => 'published',
                'published_at' => now(),
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }

    private function createPublications(): void
    {
        $categories = Category::all();
        $tags = Tag::all();

        $publications = [
            [
                'title' => 'Shariah-Compliant Cryptocurrency: A Comprehensive Guide',
                'slug' => 'shariah-compliant-cryptocurrency-guide',
                'abstract' => 'This comprehensive guide examines the Islamic perspective on cryptocurrency and blockchain technology, providing detailed analysis of Shariah compliance requirements for digital assets.',
                'authors' => [
                    ['name' => 'Dr. Ahmad Al-Rashid', 'affiliation' => 'HEAC'],
                    ['name' => 'Sheikh Muhammad Al-Qadir', 'affiliation' => 'HEAC Shariah Board'],
                ],
                'publication_date' => now()->subMonths(1),
                'status' => 'published',
                'featured' => true,
            ],
            [
                'title' => 'Sukuk Market Analysis 2024',
                'slug' => 'sukuk-market-analysis-2024',
                'abstract' => 'An in-depth analysis of the global Sukuk market, examining trends, opportunities, and challenges in Islamic bond issuance and investment.',
                'authors' => [
                    ['name' => 'Dr. Fatima Hassan', 'affiliation' => 'HEAC Research'],
                ],
                'publication_date' => now()->subMonths(2),
                'status' => 'published',
                'featured' => true,
            ],
            [
                'title' => 'Halal Business Certification Standards',
                'slug' => 'halal-business-certification-standards',
                'abstract' => 'A comprehensive overview of halal certification standards for businesses, covering food, cosmetics, pharmaceuticals, and services sectors.',
                'authors' => [
                    ['name' => 'Sheikh Abdullah Al-Mansour', 'affiliation' => 'HEAC'],
                ],
                'publication_date' => now()->subMonths(3),
                'status' => 'published',
                'featured' => false,
            ],
        ];

        foreach ($publications as $pubData) {
            $publication = Research::updateOrCreate(
                ['slug' => $pubData['slug']],
                $pubData
            );
            
            if ($publication->wasRecentlyCreated || !$publication->categories()->count()) {
                $randomCategories = $categories->random(min(2, $categories->count()));
                $publication->categories()->sync($randomCategories->pluck('id'));
                
                $randomTags = $tags->random(min(3, $tags->count()));
                $publication->tags()->sync($randomTags->pluck('id'));
            }
        }
    }
}
