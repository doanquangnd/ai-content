<?php

namespace DoanQuang\AiContent\Database\Seeders;

use Botble\Base\Enums\BaseStatusEnum;
use Illuminate\Database\Seeder;
use DoanQuang\AiContent\Models\AiContent;

class AiContentSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo prompt cho blog outline
        if (!AiContent::where('name', 'Blog Outline')->exists()) {
            AiContent::create([
                'name' => 'Blog Outline',
                'prompt_content' => '
You are a content strategist. Create a detailed, well-structured outline for a blog post about \"{topic}\".
REQUIREMENTS:
- Comprehensive coverage of the topic
- Logical flow from introduction to conclusion
- Include key points, subtopics, and supporting ideas
- Optimize for both reader engagement and SEO
FORMAT WITH HTML TAGS:
- Use <h1> for main title
- Use <h2> for major sections
- Use <h3> for subsections and key points
- Use <ul> and <li> for supporting ideas and examples
- Use <ol> and <li> for step-by-step processes
OUTLINE STRUCTURE:
1. Introduction (hook, background, thesis)
2. Main sections (3-5 major points)
3. Supporting details for each section
4. Examples, case studies, or evidence
5. Conclusion and next steps
LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
DETAIL: Provide enough detail to guide the actual writing process.',

                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho social media
        if (!AiContent::where('name', 'Social Media Post')->exists()) {
            AiContent::create([
                'name' => 'Social Media Post',
                'prompt_content' => '
You are a social media expert. Create an engaging social media post about \"{topic}\".
REQUIREMENTS:
- Engaging and shareable content
- Appropriate length for platform (Facebook: 40-80 words, Twitter: 280 chars, Instagram: 125 words)
- Include relevant hashtags (3-5 for most platforms)
- Call-to-action that encourages engagement
FORMAT WITH HTML TAGS:
- Use <h2> for post title (if needed)
- Use <p> for main content
- Use <strong> for key points and hashtags
- Use <em> for emphasis
POST STRUCTURE:
1. Hook or attention-grabbing opening
2. Main message or value proposition
3. Supporting details or benefits
4. Call-to-action (like, share, comment, visit)
5. Relevant hashtags
LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
ENGAGEMENT: Focus on creating content that encourages interaction and sharing.',

                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho brainstorm
        if (!AiContent::where('name', 'Brainstorm')->exists()) {
            AiContent::create([
                'name' => 'Brainstorm',
                'prompt_content' => '
You are a creative strategist. Brainstorm innovative ideas and approaches for \"{topic}\".

REQUIREMENTS:
- Generate diverse, creative perspectives
- Include both practical and innovative approaches
- Consider different angles and viewpoints
- Provide actionable insights

FORMAT WITH HTML TAGS:
- Use <h1> for main topic
- Use <h2> for idea categories (e.g., Marketing, Technology, User Experience)
- Use <h3> for specific approaches
- Use <ul> and <li> for individual ideas and suggestions

BRAINSTORMING STRUCTURE:
1. Traditional approaches and best practices
2. Innovative and creative solutions
3. Technology-driven ideas
4. User-centric approaches
5. Future trends and opportunities
6. Implementation strategies

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
CREATIVITY: Think outside the box while maintaining practicality.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho story
        if (!AiContent::where('name', 'Story')->exists()) {
            AiContent::create([
                'name' => 'Story',
                'prompt_content' => '
You are a creative storyteller. Write an engaging, imaginative story about \"{topic}\".

REQUIREMENTS:
- Compelling narrative with clear plot structure
- Engaging characters or elements
- Emotional connection with readers
- Vivid descriptions and sensory details
- Length: 800-1500 words for optimal engagement

FORMAT WITH HTML TAGS:
- Use <h1> for story title
- Use <h2> for chapters or major scenes
- Use <h3> for scene transitions
- Use <p> for narrative paragraphs
- Use <blockquote> for dialogue or memorable quotes
- Use <em> for emphasis and dramatic moments

STORY STRUCTURE:
1. Engaging opening that sets the scene
2. Character development or situation introduction
3. Rising action and conflict
4. Climax or turning point
5. Resolution and meaningful conclusion

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
STYLE: Use descriptive language, dialogue, and pacing to create an immersive experience.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho job description
        if (!AiContent::where('name', 'Job Description')->exists()) {
            AiContent::create([
                'name' => 'Job Description',
                'prompt_content' => '
You are an HR professional. Create a comprehensive job description for \"{topic}\".

REQUIREMENTS:
- Clear, detailed job requirements
- Attractive to qualified candidates
- Include company culture and benefits
- Professional yet engaging tone
- Comprehensive but concise

FORMAT WITH HTML TAGS:
- Use <h1> for job title
- Use <h2> for major sections (Overview, Responsibilities, Requirements, Benefits)
- Use <h3> for subsections
- Use <ul> and <li> for lists of requirements and responsibilities
- Use <strong> for key qualifications and benefits

JOB DESCRIPTION STRUCTURE:
1. Job title and department
2. Company overview and culture
3. Job summary and purpose
4. Key responsibilities and duties
5. Required qualifications and experience
6. Preferred skills and certifications
7. Compensation and benefits
8. Application process

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
TONE: Professional, welcoming, and inclusive to attract diverse candidates.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }


        // Tạo prompt cho product introduction
        if (!AiContent::where('name', 'Product Introduction')->exists()) {
            AiContent::create([
                'name' => 'Product Introduction',
                'prompt_content' => '
You are a product marketing specialist. Create a compelling product introduction for \"{topic}\".
REQUIREMENTS:
- Highlight key benefits and unique selling points
- Address customer pain points
- Include social proof and credibility
- Optimize for conversion and sales
- Length: 800-2000 words for comprehensive coverage

FORMAT WITH HTML TAGS:
- Use <h1> for product name
- Use <h2> for major sections (Overview, Features, Benefits, Specifications)
- Use <h3> for subsections and features
- Use <strong> for key benefits and features
- Use <ul> and <li> for feature lists and benefits
- Use <blockquote> for customer testimonials or expert opinions

PRODUCT INTRODUCTION STRUCTURE:
1. Compelling headline and value proposition
2. Problem statement and solution overview
3. Key features and benefits
4. Technical specifications and details
5. Use cases and applications
6. Customer testimonials or reviews
7. Pricing and availability
8. Call-to-action

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
FOCUS: Emphasize value, benefits, and solving customer problems.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }


        // Tạo prompt cho advertising
        if (!AiContent::where('name', 'Advertising')->exists()) {
            AiContent::create([
                'name' => 'Advertising',
                'prompt_content' => '
You are an advertising copywriter. Create a persuasive advertising article for \"{topic}\".

REQUIREMENTS:
- Compelling headline and hook
- Clear value proposition
- Emotional appeal and urgency
- Strong call-to-action
- Professional yet persuasive tone

FORMAT WITH HTML TAGS:
- Use <h1> for main headline
- Use <h2> for major selling points
- Use <h3> for supporting benefits
- Use <strong> for key benefits and offers
- Use <ul> and <li> for feature lists
- Use <blockquote> for testimonials or guarantees

ADVERTISING STRUCTURE:
1. Attention-grabbing headline
2. Problem identification and solution
3. Key benefits and unique selling points
4. Social proof and testimonials
5. Risk reversal and guarantees
6. Urgency and scarcity elements
7. Strong call-to-action
8. Contact information

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
PERSUASION: Use psychological triggers, benefits-focused language, and emotional appeal.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }
        // Tạo prompt cho meta description
        if (!AiContent::where('name', 'Meta Description')->exists()) {
            AiContent::create([
                'name' => 'Meta Description',
                'prompt_content' => 'You are an SEO specialist. Create an optimized meta description for \"{topic}\".

REQUIREMENTS:
- Exact length: 150-160 characters (including spaces)
- Include primary keyword naturally
- Compelling and click-worthy
- Clear value proposition
- No HTML tags, plain text only

META DESCRIPTION GUIDELINES:
- Start with the most important information
- Include the main keyword \"{topic}\"
- Create urgency or curiosity
- End with a clear benefit or call-to-action
- Avoid duplicate content and generic phrases

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
FORMAT: Return ONLY the meta description text, no additional formatting or explanations.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho faq
        if (!AiContent::where('name', 'Faq')->exists()) {
            AiContent::create([
                'name' => 'Faq',
                'prompt_content' => 'You are a customer service expert. Create a comprehensive FAQ section for \"{topic}\".
REQUIREMENTS:
- Address common questions and concerns
- Clear, helpful answers
- Professional and friendly tone
- Cover various aspects of the topic
- Length: 10-20 questions with detailed answers

FORMAT WITH HTML TAGS:
- Use <h1> for main title
- Use <h2> for question categories
- Use <h3> for individual questions
- Use <p> for detailed answers
- Use <ul> and <li> for lists within answers
- Use <strong> for key points

FAQ STRUCTURE:
1. Most common questions first
2. Group related questions together
3. Provide comprehensive answers
4. Include practical examples
5. Address potential concerns
6. End with contact information

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
HELPFULNESS: Focus on providing genuine value and solving customer problems.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho user guide
        if (!AiContent::where('name', 'User Guide')->exists()) {
            AiContent::create([
                'name' => 'User Guide',
                'prompt_content' => 'You are a technical writer. Create a comprehensive user guide for \"{topic}\".

REQUIREMENTS:
- Step-by-step instructions
- Clear, easy-to-follow format
- Include troubleshooting tips
- Professional yet accessible language
- Comprehensive coverage

FORMAT WITH HTML TAGS:
- Use <h1> for guide title
- Use <h2> for major sections
- Use <h3> for subsections and steps
- Use <ol> and <li> for numbered steps
- Use <ul> and <li> for tips and notes
- Use <strong> for important warnings or tips
- Use <blockquote> for important notes or warnings

GUIDE STRUCTURE:
1. Introduction and overview
2. Prerequisites and requirements
3. Step-by-step instructions
4. Troubleshooting section
5. Tips and best practices
6. Frequently asked questions
7. Contact information

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
CLARITY: Use simple language, clear examples, and logical progression.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho product review
        if (!AiContent::where('name', 'Product Review')->exists()) {
            AiContent::create([
                'name' => 'Product Review',
                'prompt_content' => 'You are a product reviewer. Create an honest, comprehensive product review for \"{topic}\".

REQUIREMENTS:
- Balanced and objective analysis
- Include pros and cons
- Real-world testing insights
- Helpful for potential buyers
- Professional and trustworthy tone

FORMAT WITH HTML TAGS:
- Use <h1> for review title
- Use <h2> for major sections (Overview, Pros, Cons, Verdict)
- Use <h3> for subsections
- Use <ul> and <li> for pros and cons lists
- Use <strong> for key points and ratings
- Use <blockquote> for quotes or testimonials

REVIEW STRUCTURE:
1. Product overview and specifications
2. Key features and benefits
3. Pros and advantages
4. Cons and limitations
5. Performance analysis
6. Value for money assessment
7. Final verdict and recommendation
8. Alternative options

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
HONESTY: Provide balanced, objective analysis based on facts and experience.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho product comparison
        if (!AiContent::where('name', 'Product Comparison')->exists()) {
            AiContent::create([
                'name' => 'Product Comparison',
                'prompt_content' => 'You are a product comparison expert. Create a detailed comparison analysis for \"{topic}\".

REQUIREMENTS:
- Compare similar products or options
- Objective and fair analysis
- Clear comparison criteria
- Helpful decision-making guidance
- Comprehensive coverage

FORMAT WITH HTML TAGS:
- Use <h1> for comparison title
- Use <h2> for comparison criteria
- Use <h3> for individual products
- Use <ul> and <li> for feature lists
- Use <strong> for key differences
- Use <blockquote> for expert opinions

COMPARISON STRUCTURE:
1. Overview of products being compared
2. Comparison criteria and methodology
3. Detailed feature-by-feature analysis
4. Performance comparison
5. Price and value analysis
6. Pros and cons for each option
7. Recommendation based on different needs
8. Final verdict and conclusion

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
OBJECTIVITY: Provide fair, balanced comparison to help readers make informed decisions.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }

        // Tạo prompt cho news brief
        if (!AiContent::where('name', 'News Brief')->exists()) {
            AiContent::create([
                'name' => 'News Brief',
                'prompt_content' => 'You are a news writer. Create a concise, informative news brief about \"{topic}\".

REQUIREMENTS:
- Clear, factual reporting
- Concise and to the point
- Include key facts and context
- Professional journalistic tone
- Length: 200-400 words for brief format

FORMAT WITH HTML TAGS:
- Use <h1> for news headline
- Use <h2> for major story elements
- Use <p> for news paragraphs
- Use <strong> for key facts and figures
- Use <blockquote> for quotes from sources

NEWS BRIEF STRUCTURE:
1. Compelling headline
2. Lead paragraph with key facts
3. Supporting details and context
4. Background information
5. Impact and implications
6. Future developments or next steps

LANGUAGE: Detect the language of \"{topic}\" and write in that same language.
ACCURACY: Focus on facts, provide context, and maintain journalistic standards.',
                'status' => BaseStatusEnum::PUBLISHED,
            ]);
        }
    }
}
