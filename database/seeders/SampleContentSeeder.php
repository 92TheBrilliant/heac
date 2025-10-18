<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Research;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample categories...');
        $this->createCategories();
        
        $this->command->info('Creating sample tags...');
        $this->createTags();
        
        $this->command->info('Creating sample pages...');
        $this->createPages();
        
        $this->command->info('Creating sample research...');
        $this->createResearch();
        
        $this->command->info('Sample content created successfully!');
    }

    /**
     * Create sample categories
     */
    private function createCategories(): void
    {
        $categories = [
            [
                'name' => 'Quality Assurance',
                'slug' => 'quality-assurance',
                'description' => 'Research related to quality assurance in higher education',
            ],
            [
                'name' => 'Accreditation Standards',
                'slug' => 'accreditation-standards',
                'description' => 'Studies on accreditation standards and frameworks',
            ],
            [
                'name' => 'Higher Education Policy',
                'slug' => 'higher-education-policy',
                'description' => 'Policy research and analysis in higher education',
            ],
            [
                'name' => 'Institutional Assessment',
                'slug' => 'institutional-assessment',
                'description' => 'Research on institutional assessment methodologies',
            ],
            [
                'name' => 'Academic Programs',
                'slug' => 'academic-programs',
                'description' => 'Studies on academic program development and evaluation',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }

    /**
     * Create sample tags
     */
    private function createTags(): void
    {
        $tags = [
            'Quality Standards',
            'Best Practices',
            'Assessment',
            'Evaluation',
            'Benchmarking',
            'Compliance',
            'Innovation',
            'Student Outcomes',
            'Faculty Development',
            'Curriculum Design',
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

    /**
     * Create sample pages
     */
    private function createPages(): void
    {
        $pages = [
            [
                'title' => 'About HEAC',
                'slug' => 'about',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'About the Higher Education Accreditation Commission',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'The Higher Education Accreditation Commission (HEAC) is the official body responsible for quality assurance and accreditation of higher education institutions in Jordan.',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'Our mission is to ensure the quality and standards of higher education through rigorous evaluation and continuous improvement processes.',
                    ],
                ],
                'excerpt' => 'Learn about HEAC\'s mission and role in higher education quality assurance.',
                'meta_title' => 'About HEAC - Higher Education Accreditation Commission',
                'meta_description' => 'The Higher Education Accreditation Commission ensures quality and standards in Jordanian higher education.',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'Get in Touch',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'We welcome your inquiries and feedback. Please use the contact form below to reach us.',
                    ],
                ],
                'excerpt' => 'Contact the Higher Education Accreditation Commission.',
                'meta_title' => 'Contact HEAC',
                'meta_description' => 'Get in touch with the Higher Education Accreditation Commission.',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Accreditation Process',
                'slug' => 'accreditation-process',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'Understanding the Accreditation Process',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'The accreditation process involves several stages designed to ensure comprehensive evaluation of educational institutions.',
                    ],
                    [
                        'type' => 'heading',
                        'content' => 'Key Stages',
                    ],
                    [
                        'type' => 'list',
                        'content' => [
                            'Self-assessment and documentation',
                            'Site visit by evaluation team',
                            'Review and analysis',
                            'Decision and recommendations',
                            'Follow-up and continuous improvement',
                        ],
                    ],
                ],
                'excerpt' => 'Learn about the stages of institutional accreditation.',
                'meta_title' => 'Accreditation Process - HEAC',
                'meta_description' => 'Understand the comprehensive accreditation process for higher education institutions.',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Quality Standards',
                'slug' => 'quality-standards',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'Quality Standards in Higher Education',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'HEAC has established comprehensive quality standards that institutions must meet to achieve and maintain accreditation.',
                    ],
                ],
                'excerpt' => 'Explore the quality standards for higher education institutions.',
                'meta_title' => 'Quality Standards - HEAC',
                'meta_description' => 'Comprehensive quality standards for higher education accreditation.',
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'News and Updates',
                'slug' => 'news',
                'content' => [
                    [
                        'type' => 'heading',
                        'content' => 'Latest News and Updates',
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => 'Stay informed about the latest developments in higher education accreditation.',
                    ],
                ],
                'excerpt' => 'Latest news and updates from HEAC.',
                'meta_title' => 'News - HEAC',
                'meta_description' => 'Latest news and updates from the Higher Education Accreditation Commission.',
                'status' => 'published',
                'published_at' => now(),
            ],
        ];

        foreach ($pages as $pageData) {
            Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }
    }

    /**
     * Create sample research
     */
    private function createResearch(): void
    {
        $categories = Category::all();
        $tags = Tag::all();

        $researchItems = [
            [
                'title' => 'Quality Assurance Framework for Higher Education Institutions',
                'slug' => 'quality-assurance-framework',
                'abstract' => 'This comprehensive study examines the implementation of quality assurance frameworks in higher education institutions across Jordan. The research analyzes best practices, challenges, and recommendations for effective quality management systems.',
                'authors' => [
                    ['name' => 'Dr. Ahmad Al-Hassan', 'affiliation' => 'HEAC'],
                    ['name' => 'Dr. Layla Mahmoud', 'affiliation' => 'University of Jordan'],
                ],
                'publication_date' => now()->subMonths(2),
                'status' => 'published',
                'featured' => true,
            ],
            [
                'title' => 'Impact of Accreditation on Institutional Performance',
                'slug' => 'impact-of-accreditation',
                'abstract' => 'An empirical study investigating the relationship between accreditation status and institutional performance metrics. The research provides evidence-based insights into the benefits of accreditation for higher education institutions.',
                'authors' => [
                    ['name' => 'Dr. Fatima Al-Rashid', 'affiliation' => 'HEAC'],
                    ['name' => 'Prof. Khaled Ibrahim', 'affiliation' => 'Jordan University of Science and Technology'],
                ],
                'publication_date' => now()->subMonths(4),
                'status' => 'published',
                'featured' => true,
            ],
            [
                'title' => 'Best Practices in Academic Program Assessment',
                'slug' => 'best-practices-program-assessment',
                'abstract' => 'This paper explores effective methodologies for assessing academic programs, including learning outcomes assessment, curriculum evaluation, and continuous improvement strategies.',
                'authors' => [
                    ['name' => 'Dr. Nadia Saleh', 'affiliation' => 'HEAC'],
                ],
                'publication_date' => now()->subMonths(6),
                'status' => 'published',
                'featured' => false,
            ],
            [
                'title' => 'Student Learning Outcomes Assessment: A Comprehensive Guide',
                'slug' => 'student-learning-outcomes-guide',
                'abstract' => 'A practical guide for institutions on developing and implementing effective student learning outcomes assessment systems. Includes case studies and implementation frameworks.',
                'authors' => [
                    ['name' => 'Dr. Omar Khalil', 'affiliation' => 'HEAC'],
                    ['name' => 'Dr. Rania Mustafa', 'affiliation' => 'Yarmouk University'],
                ],
                'publication_date' => now()->subMonths(8),
                'status' => 'published',
                'featured' => false,
            ],
            [
                'title' => 'Benchmarking Standards in Higher Education',
                'slug' => 'benchmarking-standards',
                'abstract' => 'An analysis of international benchmarking practices and their application to the Jordanian higher education context. The study provides recommendations for developing context-appropriate standards.',
                'authors' => [
                    ['name' => 'Prof. Sami Al-Zoubi', 'affiliation' => 'HEAC'],
                ],
                'publication_date' => now()->subMonths(10),
                'status' => 'published',
                'featured' => false,
            ],
            [
                'title' => 'Faculty Development and Quality Enhancement',
                'slug' => 'faculty-development-quality',
                'abstract' => 'This research examines the role of faculty development programs in enhancing educational quality. It presents evidence-based strategies for effective professional development initiatives.',
                'authors' => [
                    ['name' => 'Dr. Hala Qasem', 'affiliation' => 'HEAC'],
                    ['name' => 'Dr. Tariq Nasser', 'affiliation' => 'German Jordanian University'],
                ],
                'publication_date' => now()->subYear(),
                'status' => 'published',
                'featured' => false,
            ],
        ];

        foreach ($researchItems as $researchData) {
            $research = Research::firstOrCreate(
                ['slug' => $researchData['slug']],
                $researchData
            );
            
            // Only attach relationships if this is a new record
            if ($research->wasRecentlyCreated) {
                // Attach random categories (1-2 per research)
                $randomCategories = $categories->random(rand(1, 2));
                $research->categories()->attach($randomCategories->pluck('id'));
                
                // Attach random tags (2-4 per research)
                $randomTags = $tags->random(rand(2, 4));
                $research->tags()->attach($randomTags->pluck('id'));
            }
        }
    }
}
