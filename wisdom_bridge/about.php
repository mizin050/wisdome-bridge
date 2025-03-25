
<?php
require_once 'config.php';
include 'header.php';
?>

<div class="max-w-4xl mx-auto mt-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Hero section -->
        <div class="relative bg-blue-600 text-white p-10 mb-6">
            <h1 class="text-4xl font-bold mb-4">Welcome to Wisdom Bridge</h1>
            <p class="text-xl opacity-90">Connecting knowledge seekers with experts</p>
            <div class="absolute bottom-0 right-0 opacity-20">
                <svg xmlns="http://www.w3.org/2000/svg" width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
            </div>
        </div>

        <!-- Main content -->
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">About Our Platform</h2>
                    <p class="text-gray-600 mb-4">
                        Wisdom Bridge is a knowledge-sharing platform designed to connect learners with experts across various subjects. Our mission is to create a collaborative environment where questions are met with thoughtful, accurate answers.
                    </p>
                    <p class="text-gray-600 mb-4">
                        Founded in 2023, we've grown to support thousands of users who ask questions, share insights, and build their expertise in fields ranging from computer science to philosophy.
                    </p>
                    <div class="mt-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">How It Works</h3>
                        <ul class="list-disc pl-5 text-gray-600 space-y-2">
                            <li>Sign up for a free account</li>
                            <li>Ask questions on topics that interest you</li>
                            <li>Answer questions to share your knowledge</li>
                            <li>Upvote helpful answers to recognize quality</li>
                            <li>Earn reputation and build your profile</li>
                        </ul>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Our Features</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Expert Q&A</h3>
                                <p class="text-gray-600">Get your questions answered by subject matter experts and enthusiasts.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Verified Answers</h3>
                                <p class="text-gray-600">Community voting helps surface the most accurate and helpful information.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Subject-Focused</h3>
                                <p class="text-gray-600">Browse questions by subject to find exactly what you're looking for.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <a href="index.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            Browse Questions
                        </a>
                        <?php if (!isLoggedIn()): ?>
                            <a href="signup.php" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-6 rounded-lg ml-3 transition-colors">
                                Join Today
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Stats section -->
            <div class="mt-12 border-t pt-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Our Community in Numbers</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600">5000+</div>
                        <div class="text-gray-600">Active Users</div>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600">12,000+</div>
                        <div class="text-gray-600">Questions Asked</div>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600">35,000+</div>
                        <div class="text-gray-600">Answers Shared</div>
                    </div>
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600">98%</div>
                        <div class="text-gray-600">Satisfaction Rate</div>
                    </div>
                </div>
            </div>
            
            <!-- Call to action -->
            <div class="mt-12 bg-gray-50 p-8 rounded-lg text-center">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Ready to expand your knowledge?</h2>
                <p class="text-gray-600 mb-6 max-w-2xl mx-auto">Join our community today and start your journey of learning and sharing knowledge with experts and enthusiasts worldwide.</p>
                <?php if (!isLoggedIn()): ?>
                    <div class="flex justify-center space-x-4">
                        <a href="signup.php" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            Create Account
                        </a>
                        <a href="signin.php" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors">
                            Sign In
                        </a>
                    </div>
                <?php else: ?>
                    <a href="ask.php" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                        Ask Your First Question
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
