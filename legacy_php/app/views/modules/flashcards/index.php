<div class="container mx-auto px-4 py-6" id="flashcards-container">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gradient mb-2">🧠 Flashcards</h1>
        <p class="text-text-secondary">Create and study flashcards to memorize important information</p>
    </div>

    <!-- Stats Cards -->
    <div class="flex_full flex grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" id="stats-container">
        <div class="col-span-4 text-center py-8">
            <div class="spinner"></div>
            <p class="mt-2 text-text-secondary">Loading statistics...</p>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="card mb-6">
        <div class="card-header flex_full">
            <div class="flex flex_full items-center justify-between">
                <div class="flex space-x-2" id="main-tabs">
                    <button class="tab-btn active" data-tab="sets">📚 My Sets</button>
                    <button class="tab-btn" data-tab="study">🎓 Study Now</button>
                    <button class="tab-btn" data-tab="review">🔄 Review</button>
                </div>
                <button class="btn btn-primary" id="create-set-btn">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="mr-2">
                        <path d="M8 3V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    New Set
                </button>
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="p-6">
            <!-- Sets Tab -->
            <div id="sets-tab" class="tab-content active">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="sets-container">
                    <div class="col-span-3 text-center py-12">
                        <div class="spinner"></div>
                        <p class="mt-4 text-text-secondary">Loading your flashcard sets...</p>
                    </div>
                </div>
            </div>

            <!-- Study Tab -->
            <div id="study-tab" class="tab-content">
                <div class="text-center py-12" id="study-container">
                    <div class="max-w-md mx-auto">
                        <h3 class="text-xl font-semibold mb-4">Select a set to study</h3>
                        <div class="grid grid-cols-1 gap-4" id="study-sets-list">
                            <!-- Sets for study will be loaded here -->
                        </div>
                        <div class="mt-6" id="study-progress" style="display: none;">
                            <div class="mb-4">
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium">Progress</span>
                                    <span class="text-sm font-medium" id="study-progress-text">0/0</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" id="study-progress-bar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Tab -->
            <div id="review-tab" class="tab-content">
                <div class="text-center py-12" id="review-container">
                    <h3 class="text-xl font-semibold mb-4">Cards Due for Review</h3>
                    <div id="review-cards-list">
                        <!-- Review cards will be loaded here -->
                    </div>
                    <button class="btn btn-primary mt-6" id="start-review-btn" style="display: none;">
                        🚀 Start Review Session
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Create/Edit Set -->
<div class="modal" id="set-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">Create New Set</h3>
            <button class="modal-close">x</button>
        </div>
        <div class="modal-body">
            <form id="set-form">
                <input type="hidden" id="set-id">
                <div class="form-group">
                    <label class="form-label">Set Title</label>
                    <input type="text" id="set-title" class="form-control" 
                           placeholder="e.g., Biology Chapter 1, Spanish Vocabulary" required>
                </div>
                <div class="flex justify-end space-x-3 mt-6 gap-6">
                    <button type="button" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="save-set-btn">
                        <span class="save-text">Save Set</span>
                        <span class="spinner-text" style="display: none;">
                            <div class="spinner-small mr-2"></div>Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Create/Edit Card -->
<div class="modal" id="card-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="card-modal-title">Add New Card</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="card-form">
                <input type="hidden" id="card-id">
                <input type="hidden" id="card-set-id">
                <div class="form-group">
                    <label class="form-label">Question</label>
                    <textarea id="card-question" class="form-control" rows="3" 
                              placeholder="Enter the question or term" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Answer</label>
                    <textarea id="card-answer" class="form-control" rows="3" 
                              placeholder="Enter the answer or definition" required></textarea>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" class="btn btn-secondary modal-close">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="save-card-btn">
                        <span class="save-text">Save Card</span>
                        <span class="spinner-text" style="display: none;">
                            <div class="spinner-small mr-2"></div>Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Study Modal -->
<div class="modal" id="study-modal">
    <div class="modal-content max-w-2xl">
        <div class="modal-header">
            <div class="flex items-center justify-between w-full">
                <div>
                    <h3 class="modal-title" id="study-set-title">Study Mode</h3>
                    <p class="text-sm text-text-secondary" id="study-progress-label">Card 1 of 10</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="btn btn-icon" id="flip-card-btn" title="Flip card">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M12 20H6C4.89543 20 4 19.1046 4 18V6C4 4.89543 4.89543 4 6 4H18C19.1046 4 20 4.89543 20 6V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M16 16L20 12L16 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 12H12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <button class="modal-close">&times;</button>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div id="study-card" class="min-h-[300px] flex items-center justify-center">
                <!-- Card content will be loaded here -->
            </div>
            <!-- Buttons sẽ được cập nhật bằng JavaScript -->
            <div class="flex justify-between items-center mt-8" id="study-buttons-container">
                <!-- Dynamic buttons -->
            </div>
        </div>
    </div>
</div>