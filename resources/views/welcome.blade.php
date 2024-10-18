@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
    <h1>Project Summary and Roadmap</h1>
    <h2>Project Summary</h2>
    <p>The project aims to create a web-based platform that leverages Large Language Models (LLMs) to generate multiple texts based on user-defined templates. Users can create, manage, and visualize various categories and elements, formulate template sentences, and generate permutations of these templates to produce a wide range of text outputs. Additionally, the platform supports the generation of images related to the text outputs, providing a comprehensive tool for creative content generation and management. Core functionalities include user authentication and management, category and element management, template creation and management, prompt generation and LLM integration, image generation and management, visualization and filtering, job scheduling and notification, additional content generation, and robust database management.</p>
    <p>Enhanced functionalities will include advanced search and filtering, analytics and reporting, customizable LLM parameters, content export and import, collaboration and sharing, role-based access control, version control, integration with external tools, content moderation, customizable user interface, user feedback and ratings, AI-powered suggestions, performance optimization, scheduled content generation, and multi-language support. The design and user experience (UX/UI) will focus on creating an intuitive and responsive user interface with a clear navigation structure, ensuring a comprehensive and user-friendly platform for generating, managing, and visualizing creative content using LLMs and image generation APIs.</p>

    <h1>Roadmap with Intermediate Versions</h1>

    <h2>Version 1.0.0 (Initial Release)</h2>
    <h3>Core Functionalities:</h3>
    <ol>
        <li><strong>User Authentication and Management:</strong>
            <ul>
                <li><strong>1.0.1:</strong> User registration and login with email verification.</li>
                <li><strong>1.0.2:</strong> User profile management (update profile information, change password).</li>
                <li><strong>1.0.3:</strong> Secure password recovery options.</li>
            </ul>
        </li>
        <li><strong>Category and Element Management:</strong>
            <ul>
                <li><strong>1.0.4:</strong> CRUD operations for categories.</li>
                <li ><strong>1.0.5:</strong> CRUD operations for elements.</li>
                <li><strong>1.0.6:</strong> Add bulk delete or edit.</li>
                <li class="bg-info text-dark"><strong>1.0.6*:</strong> Arreglar validación update element.</li>
                <li ><strong>1.0.6.1:</strong> Add alert before deleting items.</li>
                <li><strong>1.0.6.2:</strong> Permitir filtrar y ordenar tablas.</li>
                <li><strong>1.0.6.3:</strong> Realizar todas las vistas en inglés.</li>
            </ul>
        </li>
        <li><strong>Template Creation and Management:</strong>
            <ul>
                <li><strong>1.0.7:</strong> CRUD operations for templates.</li>
                <li><strong>1.0.8:</strong> Automatic generation of permutations of elements within categories.</li>
                <li><strong>1.0.9:</strong> Validation for template placeholders.</li>
                <li><strong>1.0.9.1:</strong> Realizar todas las vistas en inglés.</li>
            </ul>
        </li>
        <li><strong>Prompt Generation and LLM Integration:</strong>
            <ul>
                <li><strong>1.0.10:</strong> Automatic generation of prompts based on templates.</li>
                <li><strong>1.0.11:</strong> Integration with LLM API for generating responses.</li>
                <li><strong>1.0.12:</strong> Storage of prompts and LLM responses in the database.</li>
            </ul>
        </li>
        <li><strong>Image Generation and Management:</strong>
            <ul>
                <li><strong>1.0.13:</strong> Script to generate images for LLM responses.</li>
                <li><strong>1.0.14:</strong> CRUD operations for images (mark as favorite, delete).</li>
                <li><strong>1.0.15:</strong> Storage of images and metadata in the database.</li>
            </ul>
        </li>
        <li><strong>Visualization and Filtering:</strong>
            <ul>
                <li><strong>1.0.16:</strong> Hierarchical visualization of categories, elements, prompts, LLM responses, and images.</li>
                <li><strong>1.0.17:</strong> Basic filtering options for categories, elements, favorite texts, and images.</li>
            </ul>
        </li>
        <li><strong>Job Scheduling and Notification:<a href="https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Special-Menu-Items#navbar-notification"> [*]</a></strong>
            <ul>
                <li><strong>1.0.18:</strong> Use Laravel jobs for handling multiple LLM responses or images.</li>
                <li><strong>1.0.19:</strong> Basic notification system for job completion.</li>
            </ul>
        </li>
        <li><strong>Additional Content Generation:</strong>
            <ul>
                <li><strong>1.0.20:</strong> Regenerate texts or images using existing prompts or LLM responses.</li>
            </ul>
        </li>
        <li><strong>Database Management:</strong>
            <ul>
                <li><strong>1.0.21:</strong> Design and implement a robust database schema.</li>
                <li><strong>1.0.22:</strong> Ensure data integrity and security.</li>
            </ul>
        </li>
    </ol>

    <h2>Version 1.1.0</h2>
    <h3>Enhanced Functionalities:</h3>
    <ol>
        <li><strong>Advanced Search and Filtering:</strong>
            <ul>
                <li><strong>1.1.1:</strong> Full-text search capabilities.</li>
                <li><strong>1.1.2:</strong> Advanced filtering options (e.g., date range, specific LLM model used).</li>
            </ul>
        </li>
        <li><strong>Analytics and Reporting:</strong>
            <ul>
                <li><strong>1.1.3:</strong> Track and display usage statistics (e.g., number of prompts generated, most used categories).</li>
                <li><strong>1.1.4:</strong> Generate reports on content generation activities and performance.</li>
            </ul>
        </li>
        <li><strong>Customizable LLM Parameters:</strong>
            <ul>
                <li><strong>1.1.5:</strong> Allow users to customize parameters (e.g., temperature, max tokens) for LLM API calls.</li>
                <li><strong>1.1.6:</strong> Provide predefined configurations for common use cases.</li>
            </ul>
        </li>
        <li><strong>Content Export and Import:</strong>
            <ul>
                <li><strong>1.1.7:</strong> Allow users to export generated texts, images, and templates in various formats (e.g., CSV, JSON, PDF).</li>
                <li><strong>1.1.8:</strong> Enable users to import categories, elements, and templates from external sources.</li>
            </ul>
        </li>
    </ol>

    <h2>Version 1.2.0</h2>
    <h3>Collaboration and Sharing:</h3>
    <ol>
        <li><strong>Collaborative Templates:</strong>
            <ul>
                <li><strong>1.2.1:</strong> Allow users to share templates with other users or groups.</li>
                <li><strong>1.2.2:</strong> Shared categories and elements.</li>
            </ul>
        </li>
        <li><strong>Role-Based Access Control (RBAC):</strong>
            <ul>
                <li><strong>1.2.3:</strong> Implement roles (e.g., admin, editor, viewer) to manage permissions for different users.</li>
            </ul>
        </li>
        <li><strong>Version Control:</strong>
            <ul>
                <li><strong>1.2.4:</strong> Keep track of changes to templates, categories, and elements with version history.</li>
                <li><strong>1.2.5:</strong> Rollback functionality.</li>
            </ul>
        </li>
    </ol>

    <h2>Version 1.3.0</h2>
    <h3>Advanced Features:</h3>
    <ol>
        <li><strong>Integration with External Tools:</strong>
            <ul>
                <li><strong>1.3.1:</strong> Integrate with other APIs (e.g., translation, sentiment analysis) to enhance content generation.</li>
                <li><strong>1.3.2:</strong> Allow integration with third-party plugins or tools for additional functionalities.</li>
            </ul>
        </li>
        <li><strong>Content Moderation:</strong>
            <ul>
                <li><strong>1.3.3:</strong> Implement tools to review and moderate generated content for inappropriate or harmful content.</li>
                <li><strong>1.3.4:</strong> Allow users to report inappropriate content for review.</li>
            </ul>
        </li>
        <li><strong>Customizable User Interface:</strong>
            <ul>
                <li><strong>1.3.5:</strong> Allow users to customize the appearance of the interface (e.g., color schemes, fonts).</li>
                <li><strong>1.3.6:</strong> Enable users to customize the layout of the dashboard and other pages.</li>
            </ul>
        </li>
    </ol>

    <h2>Version 1.4.0</h2>
    <h3>User Feedback and Ratings:</h3>
    <ol>
        <li><strong>Content Ratings:</strong>
            <ul>
                <li><strong>1.4.1:</strong> Allow users to rate generated content (e.g., texts, images) and provide feedback.</li>
                <li><strong>1.4.2:</strong> Collect and display user feedback to improve the platform.</li>
            </ul>
        </li>
        <li><strong>AI-Powered Suggestions:</strong>
            <ul>
                <li><strong>1.4.3:</strong> Use AI to suggest new elements or categories based on user activity.</li>
                <li><strong>1.4.4:</strong> Provide AI-generated template suggestions for users.</li>
            </ul>
        </li>
    </ol>

    <h2>Version 1.5.0</h2>
    <h3>Performance Optimization:</h3>
    <ol>
        <li><strong>Caching Mechanisms:</strong>
            <ul>
                <li><strong>1.5.1:</strong> Implement caching to improve performance and reduce API calls.</li>
                <li><strong>1.5.2:</strong> Load balancing techniques to handle high traffic and ensure scalability.</li>
            </ul>
        </li>
        <li><strong>Scheduled Content Generation:</strong>
            <ul>
                <li><strong>1.5.3:</strong> Allow users to schedule the generation of content at specific times or intervals.</li>
                <li><strong>1.5.4:</strong> Enable recurring content generation tasks (e.g., daily, weekly).</li>
            </ul>
        </li>
        <li><strong>Multi-Language Support:</strong>
            <ul>
                <li><strong>1.5.5:</strong> Allow users to select their preferred language for the interface.</li>
                <li><strong>1.5.6:</strong> Enable the generation of content in multiple languages.</li>
            </ul>
        </li>
    </ol>
@stop

{{-- Push extra CSS --}}

@push('css')
@endpush

{{-- Push extra scripts --}}

@push('js')

@endpush