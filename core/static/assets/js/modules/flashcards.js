class FlashcardManager {
    constructor() {
        this.baseUrl = '/flashcards/api';
        this.currentSet = null;
        this.currentCards = [];
        this.currentCardIndex = 0;
        this.studyMode = false;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadStats();
        this.loadSets();
    }

    bindEvents() {
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchTab(e.target.dataset.tab));
        });

        // Create set button
        document.getElementById('create-set-btn')?.addEventListener('click', () => {
            this.openSetModal();
        });

        // Set modal
        document.getElementById('set-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveSet();
        });

        // Card modal
        document.getElementById('card-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveCard();
        });

        // Study buttons
        document.getElementById('start-review-btn')?.addEventListener('click', () => {
            this.startReviewSession();
        });

        // Modal close buttons
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', () => this.closeAllModals());
        });

        // Study modal events
        document.getElementById('flip-card-btn')?.addEventListener('click', () => {
            this.flipCard();
        });

        document.getElementById('prev-card-btn')?.addEventListener('click', () => {
            this.prevCard();
        });

        document.getElementById('next-card-btn')?.addEventListener('click', () => {
            this.nextCard();
        });

        // Progress buttons
        document.getElementById('again-btn')?.addEventListener('click', () => {
            this.updateCardProgress('again');
        });

        document.getElementById('good-btn')?.addEventListener('click', () => {
            this.updateCardProgress('good');
        });

        document.getElementById('easy-btn')?.addEventListener('click', () => {
            this.updateCardProgress('easy');
        });
    }

    async apiCall(action, data = {}, method = 'POST') {
        const url = `${this.baseUrl}?action=${action}`;

        try {
            if (method === 'GET') {
                // Build query string cho GET request
                const params = new URLSearchParams();
                for (const key in data) {
                    if (data[key] !== undefined && data[key] !== null) {
                        params.append(key, data[key]);
                    }
                }

                const fullUrl = params.toString() ? `${url}&${params.toString()}` : url;
                console.log('GET URL:', fullUrl);

                const response = await fetch(fullUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                // Kiểm tra response type
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error('Response is not JSON:', text.substring(0, 200));
                    throw new Error('Server returned non-JSON response');
                }

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.error || 'Unknown error occurred');
                }

                return result;
            } else {
                // POST request
                const formData = new FormData();
                for (const key in data) {
                    if (data[key] !== undefined && data[key] !== null) {
                        formData.append(key, data[key]);
                    }
                }

                const response = await fetch(url, {
                    method: method,
                    body: formData
                });

                // Kiểm tra response type
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error('Response is not JSON:', text.substring(0, 200));
                    throw new Error('Server returned non-JSON response');
                }

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.error || 'Unknown error occurred');
                }

                return result;
            }
        } catch (error) {
            console.error('API Error:', error);
            App.showToast(error.message || 'Failed to process request', 'error');
            throw error;
        }
    }

    async loadStats(showLoading = false) {
        try {
            if (showLoading) {
                const container = document.getElementById('stats-container');
                if (container) {
                    container.innerHTML = `
                        <div class="col-span-4 text-center py-8">
                            <div class="spinner"></div>
                            <p class="mt-2 text-text-secondary">Loading statistics...</p>
                        </div>
                    `;
                }
            }

            const result = await this.apiCall('get_stats');
            this.renderStats(result);
        } catch (error) {
            console.error('Failed to load stats:', error);
        }
    }

    renderStats(data) {
        const container = document.getElementById('stats-container');
        if (!container) return;

        const stats = [
            {
                title: 'Total Sets',
                value: data.total_sets || 0,
                color: 'bg-blue-100 text-blue-600',
                id: 'stat-total-sets'
            },
            {
                title: 'Total Cards',
                value: data.total_cards || 0,
                color: 'bg-purple-100 text-purple-600',
                id: 'stat-total-cards'
            },
            {
                title: 'Learned',
                value: data.learned_cards || 0,
                color: 'bg-green-100 text-green-600',
                id: 'stat-learned-cards'
            },
            {
                title: 'Progress',
                value: `${data.progress || 0}%`,
                color: 'bg-orange-100 text-orange-600',
                id: 'stat-progress'
            }
        ];

        // Nếu container đang có data, cập nhật từng phần tử
        const existingStats = container.querySelectorAll('.stat-card-flash');
        if (existingStats.length === stats.length) {
            // Cập nhật từng stat card
            stats.forEach((stat, index) => {
                const statCard = existingStats[index];
                if (statCard) {
                    const valueElement = statCard.querySelector('.stat-value-flash');
                    if (valueElement) {
                        // Thêm animation
                        valueElement.style.transition = 'all 0.3s ease';
                        valueElement.style.transform = 'scale(1.1)';

                        setTimeout(() => {
                            valueElement.textContent = stat.value;
                            valueElement.style.transform = 'scale(1)';
                        }, 150);
                    }

                    // Cập nhật progress bar nếu có
                    const progressBar = statCard.querySelector('.progress-bar');
                    if (progressBar && stat.progress !== undefined) {
                        progressBar.style.transition = 'width 0.5s ease';
                        progressBar.style.width = `${stat.progress}%`;

                        // Cập nhật progress text
                        const progressText = statCard.querySelector('.progress-label span:last-child');
                        if (progressText) {
                            progressText.textContent = `${stat.progress}%`;
                        }
                    }
                }
            });
        } else {
            // Render mới nếu chưa có
            container.innerHTML = stats.map(stat => `
                <div class="card w-20" id="${stat.id}">
                        <div class="stat-value-flash">${stat.value}</div>
                    <div class="stat-label-flash">${stat.title}</div>
                    ${stat.progress !== undefined ? `
                        <div class="progress mt-2">
                            <div class="progress-bar" style="width: ${stat.progress}%"></div>
                        </div>
                    ` : ''}
                </div>
            `).join('');
        }
    }

    async loadSets(showLoading = false) {
        try {
            if (showLoading) {
                const container = document.getElementById('sets-container');
                if (container) {
                    container.innerHTML = `
                        <div class="col-span-3 text-center py-12">
                            <div class="spinner"></div>
                            <p class="mt-4 text-text-secondary">Loading your flashcard sets...</p>
                        </div>
                    `;
                }
            }

            const result = await this.apiCall('get_sets');
            this.renderSets(result.sets);
        } catch (error) {
            console.error('Failed to load sets:', error);
        }
    }
    renderSets(sets) {
        const container = document.getElementById('sets-container');
        if (!container) return;

        console.log('Rendering sets:', sets);

        // Kiểm tra nếu sets không phải là mảng hoặc rỗng
        if (!sets || !Array.isArray(sets) || sets.length === 0) {
            container.innerHTML = `
                <div class="col-span-3">
                    <div class="empty-state">
                        <div class="empty-state-icon">📚</div>
                        <h3 class="empty-state-title">No flashcard sets yet</h3>
                        <p class="empty-state-description">
                            Create your first set to start learning with flashcards
                        </p>
                        <button class="btn btn-primary" id="create-first-set">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="mr-2">
                                <path d="M8 3V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Create Your First Set
                        </button>
                    </div>
                </div>
            `;

            document.getElementById('create-first-set')?.addEventListener('click', () => {
                this.openSetModal();
            });
            return;
        }

        // Tạo HTML mới
        const newHtml = sets.map(set => {
            const totalCards = parseInt(set.total_cards) || 0;
            const learnedCards = parseInt(set.learned_cards) || 0;
            const progress = totalCards > 0 ? Math.round((learnedCards / totalCards) * 100) : 0;

            const safeTitle = this.escapeHtml(set.title || 'Untitled');
            const safeSetId = this.escapeHtml(set.set_id || '');

            return `
                <div class="card" data-set-id="${safeSetId}">
                    <div class="set-card-header">
                        <h3 class="set-card-title">${safeTitle}</h3>
                        <div class="set-card-actions">
                            <button class="btn btn-icon btn-sm" 
                                    onclick="flashcards.editSet('${safeSetId}', '${safeTitle}')">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M11.3333 2.00001C11.5084 1.82492 11.7163 1.68603 11.9451 1.59079C12.1739 1.49555 12.4193 1.44574 12.6667 1.44574C12.9141 1.44574 13.1595 1.49555 13.3882 1.59079C13.617 1.68603 13.8249 1.82492 14 2.00001C14.1751 2.1751 14.314 2.38302 14.4092 2.6118C14.5044 2.84058 14.5542 3.08598 14.5542 3.33334C14.5542 3.58071 14.5044 3.8261 14.4092 4.05488C14.314 4.28366 14.1751 4.49158 14 4.66668L4.99996 13.6667L1.33329 14.6667L2.33329 11L11.3333 2.00001Z" 
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="btn btn-icon btn-sm" 
                                    onclick="flashcards.deleteSet('${safeSetId}')">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M2 4H14M5.33333 4V2.66667C5.33333 2.48986 5.40357 2.32029 5.5286 2.19526C5.65362 2.07024 5.82319 2 6 2H10C10.1768 2 10.3464 2.07024 10.4714 2.19526C10.5964 2.32029 10.6667 2.48986 10.6667 2.66667V4M12.6667 4V13.3333C12.6667 13.5101 12.5964 13.6797 12.4714 13.8047C12.3464 13.9298 12.1768 14 12 14H4C3.82319 14 3.65362 13.9298 3.5286 13.8047C3.40357 13.6797 3.33333 13.5101 3.33333 13.3333V4H12.6667Z" 
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="set-card-stats">
                        <div class="stat-item">
                            <div class="stat-value" data-stat="total-cards">${totalCards}</div>
                            <div class="stat-label">Cards</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" data-stat="learned-cards">${learnedCards}</div>
                            <div class="stat-label">Learned</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value" data-stat="progress">${progress}%</div>
                            <div class="stat-label">Progress</div>
                        </div>
                    </div>
                    
                    <div class="set-card-progress">
                        <div class="progress-label">
                            <span>Progress</span>
                            <span>${progress}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: ${progress}%"></div>
                        </div>
                    </div>
                    
                    <div class="set-card-actions-bottom">
                        <button class="btn btn-sm btn-primary flex-1" 
                                onclick="flashcards.viewSet('${safeSetId}', '${safeTitle}')">
                            View Cards
                        </button>
                        <div class="flex space-y-1 gap-1">
                            <button class="btn btn-sm btn-success" 
                                    onclick="flashcards.studySet('${safeSetId}', '${safeTitle}')">
                                Study
                            </button>
                            <button class="btn btn-sm btn-warning py-1" 
                                    onclick="flashcards.studyAllSet('${safeSetId}', '${safeTitle}')"
                                    title="Study all cards including learned ones">
                                Study All
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Nếu đã có sets, cập nhật với animation
        const existingSetCards = container.querySelectorAll('.set-card');
        if (existingSetCards.length > 0) {
            // Cập nhật từng set card có animation
            sets.forEach((set, index) => {
                const existingCard = existingSetCards[index];
                if (existingCard && existingCard.dataset.setId === set.set_id) {
                    // Cập nhật stats với animation
                    const totalCardsEl = existingCard.querySelector('[data-stat="total-cards"]');
                    const learnedCardsEl = existingCard.querySelector('[data-stat="learned-cards"]');
                    const progressEl = existingCard.querySelector('[data-stat="progress"]');
                    const progressBar = existingCard.querySelector('.progress-bar');

                    const totalCards = parseInt(set.total_cards) || 0;
                    const learnedCards = parseInt(set.learned_cards) || 0;
                    const progress = totalCards > 0 ? Math.round((learnedCards / totalCards) * 100) : 0;

                    // Animation cho số liệu
                    if (totalCardsEl) {
                        this.animateNumberChange(totalCardsEl, totalCards);
                    }
                    if (learnedCardsEl) {
                        this.animateNumberChange(learnedCardsEl, learnedCards);
                    }
                    if (progressEl) {
                        this.animateNumberChange(progressEl, `${progress}%`);
                    }
                    if (progressBar) {
                        progressBar.style.transition = 'width 0.5s ease';
                        progressBar.style.width = `${progress}%`;

                        // Cập nhật progress label
                        const progressLabel = existingCard.querySelector('.progress-label span:last-child');
                        if (progressLabel) {
                            progressLabel.textContent = `${progress}%`;
                        }
                    }
                }
            });
        } else {
            // Nếu chưa có sets, render mới
            container.innerHTML = newHtml;
        }
    }

    // Thêm hàm animate number change
    animateNumberChange(element, newValue) {
        if (!element) return;

        element.style.transition = 'all 0.3s ease';
        element.style.transform = 'scale(1.1)';
        element.style.color = '#4a6cf7'; // Highlight color

        setTimeout(() => {
            element.textContent = newValue;
            element.style.transform = 'scale(1)';

            setTimeout(() => {
                element.style.color = ''; // Reset color
            }, 300);
        }, 150);
    }

    switchTab(tabName) {
        // Update active tab button
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tabName);
        });

        // Show active tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.toggle('active', content.id === `${tabName}-tab`);
        });

        // Load data for the tab
        if (tabName === 'study') {
            this.loadStudySets();
        } else if (tabName === 'review') {
            this.loadReviewCards();
        }
    }

    async loadStudySets() {
        try {
            const result = await this.apiCall('get_sets');
            this.renderStudySets(result.sets);
        } catch (error) {
            console.error('Failed to load study sets:', error);
        }
    }

    renderStudySets(sets) {
        const container = document.getElementById('study-sets-list');
        if (!container) return;

        if (!sets || sets.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">🎓</div>
                    <h3 class="empty-state-title">No sets available</h3>
                    <p class="empty-state-description">
                        Create a flashcard set first to start studying
                    </p>
                </div>
            `;
            return;
        }

        container.innerHTML = sets.map(set => {
            const totalCards = set.total_cards || 0;
            const learnedCards = set.learned_cards || 0;

            return `
                <div class="card">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="card-question">${set.title}</div>
                            <div class="text-sm text-text-secondary">
                                ${totalCards} cards • ${learnedCards} learned
                            </div>
                        </div>
                        <button class="btn btn-primary" 
                                onclick="flashcards.studySet('${set.set_id}', '${this.escapeHtml(set.title)}')">
                            Study Now
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    async loadReviewCards() {
        try {
            const result = await this.apiCall('study_cards', {}, 'GET');
            this.renderReviewCards(result.cards);
        } catch (error) {
            console.error('Failed to load review cards:', error);
            this.renderReviewCards([]);
        }
    }
    renderReviewCards(cards) {
        const container = document.getElementById('review-cards-list');
        const startBtn = document.getElementById('start-review-btn');

        if (!container) return;

        if (!cards || cards.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">🎉</div>
                    <h3 class="empty-state-title">All caught up!</h3>
                    <p class="empty-state-description">
                        No cards need review at the moment. Great job!
                    </p>
                </div>
            `;
            startBtn.style.display = 'none';
            return;
        }

        container.innerHTML = `
            <div class="text-left mb-4">
                <p class="text-text-secondary">
                    You have <strong>${cards.length}</strong> cards due for review
                </p>
            </div>
            <div class="cards-list cards-scroll">
                ${cards.slice(0, 5).map(card => `
                    <div class="card">
                        <div class="card-question">${card.question}</div>
                        <div class="card-answer">${card.answer}</div>
                    </div>
                `).join('')}
                ${cards.length > 5 ? `
                    <div class="text-center py-3 text-text-secondary">
                        + ${cards.length - 5} more cards...
                    </div>
                ` : ''}
            </div>
        `;

        startBtn.style.display = 'block';
    }

    openSetModal(setId = null, title = '') {
        const modal = document.getElementById('set-modal');
        const modalTitle = document.getElementById('modal-title');
        const setIdInput = document.getElementById('set-id');
        const setTitleInput = document.getElementById('set-title');

        if (setId) {
            modalTitle.textContent = 'Edit Set';
            setIdInput.value = setId;
            setTitleInput.value = title;
        } else {
            modalTitle.textContent = 'Create New Set';
            setIdInput.value = '';
            setTitleInput.value = '';
        }

        modal.style.display = 'flex';
        setTitleInput.focus();
    }

    async saveSet() {
        const setId = document.getElementById('set-id');
        const setTitle = document.getElementById('set-title');
        const saveBtn = document.getElementById('save-set-btn');

        if (!setId || !setTitle || !saveBtn) {
            console.error('Form elements not found');
            App.showToast('Form error: elements missing', 'error');
            return;
        }

        const title = setTitle.value.trim();

        if (!title) {
            App.showToast('Please enter a title', 'error');
            return;
        }

        // Show loading
        const saveText = saveBtn.querySelector('.save-text');
        const spinnerText = saveBtn.querySelector('.spinner-text');

        if (saveText && spinnerText) {
            saveText.style.display = 'none';
            spinnerText.style.display = 'flex';
        }
        saveBtn.disabled = true;

        try {
            const action = setId.value ? 'update_set' : 'create_set';
            const data = setId.value ?
                { set_id: setId.value, title } :
                { title };

            const result = await this.apiCall(action, data);

            App.showToast(result.message || 'Set saved successfully', 'success');
            this.closeAllModals();

            // Reload data
            this.loadStats();
            this.loadSets();

            // Nếu đang ở tab study, reload study sets
            const activeTab = document.querySelector('.tab-btn.active');
            if (activeTab && activeTab.dataset.tab === 'study') {
                this.loadStudySets();
            }
        } catch (error) {
            console.error('Failed to save set:', error);
        } finally {
            // Reset button
            if (saveText && spinnerText) {
                saveText.style.display = 'block';
                spinnerText.style.display = 'none';
            }
            saveBtn.disabled = false;
        }
    }

    async deleteSet(setId) {
        if (!confirm('Are you sure you want to delete this set? This action cannot be undone.')) {
            return;
        }

        try {
            await this.apiCall('delete_set', { set_id: setId });
            App.showToast('Set deleted successfully', 'success');

            // Reload data
            this.loadStats();
            this.loadSets();
        } catch (error) {
            console.error('Failed to delete set:', error);
        }
    }

    editSet(setId, title) {
        this.openSetModal(setId, title);
    }

    async viewSet(setId, title) {
        // Kiểm tra setId có tồn tại không
        if (!setId || setId === 'undefined') {
            App.showToast('Invalid set ID', 'error');
            return;
        }

        try {
            console.log('Loading cards for set:', setId);
            const result = await this.apiCall('get_cards', { set_id: setId }, 'GET');
            console.log('Cards loaded:', result.cards);

            if (result && result.cards) {
                this.showCardsModal(setId, title, result.cards);
            } else {
                App.showToast('No cards found in this set', 'info');
            }
        } catch (error) {
            console.error('Failed to load cards:', error);
            App.showToast('Failed to load cards', 'error');
        }
    }

    showCardsModal(setId, title, cards) {
        // Kiểm tra nếu modal đã tồn tại
        const existingModal = document.querySelector('.modal[data-set-id="' + setId + '"]');
        if (existingModal) {
            existingModal.remove();
        }

        // Tạo modal mới
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.dataset.setId = setId;
        modal.dataset.dynamic = "true"; // Đánh dấu modal động

        modal.innerHTML = `
            <div class="modal-content max-w-4xl">
                <div class="modal-header">
                    <h3 class="modal-title">${title} (${cards.length} cards)</h3>
                    <button class="modal-close">x</button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <button class="btn btn-primary" id="add-card-to-set" data-set-id="${setId}">
                            + Add New Card
                        </button>
                    </div>
                    <div class="cards-list cards-scroll" style="max-height: 400px;">
                        ${cards.map(card => `
                            <div class="card">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="card-question">${this.escapeHtml(card.question)}</div>
                                        <div class="card-answer">${this.escapeHtml(card.answer)}</div>
                                        ${card.learned == 1 ? '<span class="badge badge-success mt-2 inline-block">✅ Đã thuộc</span>' : ''}
                                    </div>
                                    <div class="card-actions flex-shrink-0 ml-4">
                                        <button class="btn btn-sm btn-secondary edit-card-btn" 
                                                data-card-id="${card.card_id}"
                                                data-question="${this.escapeHtml(card.question)}"
                                                data-answer="${this.escapeHtml(card.answer)}"
                                                data-set-id="${setId}">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-card-btn" 
                                                data-card-id="${card.card_id}">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                        ${cards.length === 0 ? `
                            <div class="empty-state py-8">
                                <div class="empty-state-icon">📝</div>
                                <h3 class="empty-state-title">No cards yet</h3>
                                <p class="empty-state-description">
                                    Add your first card to this set
                                </p>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Show modal
        modal.style.display = 'flex';

        // Add event listeners
        const closeBtn = modal.querySelector('.modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.remove();
            });
        }

        // Close on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });

        // Add card button
        const addCardBtn = modal.querySelector('#add-card-to-set');
        if (addCardBtn) {
            addCardBtn.addEventListener('click', (e) => {
                const setId = e.target.dataset.setId;
                this.openCardModal(setId);
            });
        }

        // Edit card buttons
        const editButtons = modal.querySelectorAll('.edit-card-btn');
        editButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const cardId = e.target.dataset.cardId;
                const question = e.target.dataset.question;
                const answer = e.target.dataset.answer;
                const setId = e.target.dataset.setId;
                this.editCard(cardId, question, answer, setId);
            });
        });

        // Delete card buttons
        const deleteButtons = modal.querySelectorAll('.delete-card-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const cardId = e.target.dataset.cardId;
                this.deleteCard(cardId);
            });
        });
    }

    async studySet(setId, title) {
        // Kiểm tra setId có hợp lệ không
        if (!setId || setId === 'undefined') {
            App.showToast('Invalid set ID', 'error');
            return;
        }

        try {
            // Lấy cards chưa thuộc
            const result = await this.apiCall('get_unlearned_cards', { set_id: setId }, 'GET');

            // Kiểm tra nếu có cards
            if (result.cards && result.cards.length > 0) {
                this.startStudySession(result.cards, title, setId, 'unlearned');
            } else {
                // Nếu không có card chưa thuộc, hỏi có muốn học tất cả không
                this.promptStudyAll(setId, title);
            }
        } catch (error) {
            console.error('Failed to load cards for study:', error);
            App.showToast('Failed to load cards', 'error');
        }
    }

    startStudySession(cards, title = 'Study Session', setId = null, mode = 'unlearned') {
        if (!cards || cards.length === 0) {
            App.showToast('No cards available for study', 'warning');
            return;
        }

        this.currentCards = cards;
        this.currentCardIndex = 0;
        this.studyMode = true;
        this.currentSetId = setId;
        this.studyModeType = mode; // 'unlearned' hoặc 'all'

        this.openStudyModal(title, mode);
        this.showCard();
    }

    startReviewSession() {
        this.apiCall('study_cards')
            .then(result => {
                if (result.cards && result.cards.length > 0) {
                    this.startStudySession(result.cards, 'Review Session');
                } else {
                    App.showToast('No cards need review', 'info');
                }
            })
            .catch(error => {
                console.error('Failed to start review session:', error);
            });
    }

    openStudyModal(title, mode = 'unlearned') {
        const modal = document.getElementById('study-modal');
        const studyTitle = mode === 'all' ? `${title} (All Cards)` : title;
        const studySetTitle = document.getElementById('study-set-title');
        if (studySetTitle) {
            studySetTitle.textContent = studyTitle;
        }

        // Hiển thị mode hiện tại
        const modeBadge = mode === 'all' ?
            '<span class="badge badge-warning ml-2">All Cards</span>' :
            '<span class="badge badge-primary ml-2">Unlearned Only</span>';

        if (studySetTitle) {
            studySetTitle.innerHTML = `Study Mode ${modeBadge}`;
        }

        // Tạo buttons dựa trên mode
        const buttonsContainer = document.getElementById('study-buttons-container');
        if (buttonsContainer) {
            buttonsContainer.innerHTML = `
                <button class="btn btn-secondary" id="prev-card-btn" disabled>
                    ← Previous
                </button>
                <div class="flex gap-2 space-x-2">
                    <button class="btn btn-warning" id="not-learned-btn">
                        ❌ Chưa thuộc
                    </button>
                    <button class="btn btn-success" id="learned-btn">
                        ✅ Đã thuộc
                    </button>
                </div>
                <button class="btn btn-secondary" id="next-card-btn">
                    Next →
                </button>
            `;
        }

        modal.style.display = 'flex';

        // Re-attach event listeners
        this.attachStudyEventListeners();
    }

    showCard() {
        if (this.currentCardIndex >= this.currentCards.length) {
            // Session complete
            this.endStudySession();
            return;
        }

        const card = this.currentCards[this.currentCardIndex];
        const container = document.getElementById('study-card');
        const progressLabel = document.getElementById('study-progress-label');

        // Hiển thị trạng thái hiện tại của card
        const isLearned = card.progress_status === 'learned' || card.learned == 1;
        const cardStatus = isLearned ?
            '<div class="text-sm text-text-secondary mt-2 status-label">(Đã thuộc)</div>' :
            '<div class="text-sm text-text-secondary mt-2 status-label">(Chưa thuộc)</div>';

        container.innerHTML = `
            <div class="study-card" id="current-study-card">
                <div class="study-card-inner">
                    <div class="study-card-front">
                        <div class="card-content">
                            <div>${card.question}</div>
                            ${cardStatus}
                        </div>
                        <div class="hint">Click or press space to flip</div>
                    </div>
                    <div class="study-card-back">
                        <div class="card-content">
                            <div>${card.answer}</div>
                            ${cardStatus.replace('text-text-secondary', 'text-white/80')}
                        </div>
                        <div class="hint">Click or press space to flip back</div>
                    </div>
                </div>
            </div>
        `;

        // Update progress
        const current = this.currentCardIndex + 1;
        const total = this.currentCards.length;
        const remaining = this.studyModeType === 'all' ? '' : ` (${this.currentCards.length} remaining)`;
        progressLabel.textContent = `Card ${current} of ${total}${remaining}`;

        // Update buttons
        document.getElementById('prev-card-btn').disabled = this.currentCardIndex === 0;
        const nextBtn = document.getElementById('next-card-btn');
        if (nextBtn) {
            nextBtn.disabled = this.currentCardIndex === this.currentCards.length - 1 &&
                this.studyModeType !== 'all';
        }

        // Add click event for flip
        const studyCard = document.getElementById('current-study-card');
        if (studyCard) {
            studyCard.addEventListener('click', () => this.flipCard());
        }

        // Add keyboard shortcut
        document.addEventListener('keydown', this.handleStudyKeydown.bind(this));
    }

    handleStudyKeydown(e) {
        if (!this.studyMode) return;

        switch (e.key) {
            case ' ':
            case 'Spacebar':
                e.preventDefault();
                this.flipCard();
                break;
            case 'ArrowLeft':
                e.preventDefault();
                if (this.currentCardIndex > 0) this.prevCard();
                break;
            case 'ArrowRight':
                e.preventDefault();
                if (this.currentCardIndex < this.currentCards.length - 1) this.nextCard();
                break;
            case '1':
                e.preventDefault();
                this.updateCardProgress('new');
                break;
            case '2':
                e.preventDefault();
                this.updateCardProgress('learned');
                break;
            case 'Escape':
                e.preventDefault();
                this.closeAllModals();
                break;
        }
    }

    flipCard() {
        const card = document.getElementById('current-study-card');
        if (card) {
            card.classList.toggle('flipped');
        }
    }

    prevCard() {
        if (this.currentCardIndex > 0) {
            this.currentCardIndex--;
            this.showCard();
        }
    }

    nextCard() {
        if (this.currentCardIndex < this.currentCards.length - 1) {
            this.currentCardIndex++;
            this.showCard();
        } else {
            this.endStudySession();
        }
    }

    attachStudyEventListeners() {
        // Gắn lại event listeners cho các button mới
        document.getElementById('not-learned-btn')?.addEventListener('click', () => {
            this.updateCardProgress('new');
        });

        document.getElementById('learned-btn')?.addEventListener('click', () => {
            this.updateCardProgress('learned');
        });

        // Giữ lại các listeners cũ
        document.getElementById('flip-card-btn')?.addEventListener('click', () => {
            this.flipCard();
        });

        document.getElementById('prev-card-btn')?.addEventListener('click', () => {
            this.prevCard();
        });

        document.getElementById('next-card-btn')?.addEventListener('click', () => {
            this.nextCard();
        });
    }

    async updateCardProgress(status) {
        const card = this.currentCards[this.currentCardIndex];
        if (!card) return;

        try {
            // Update progress
            await this.apiCall('update_progress', {
                card_id: card.card_id,
                status: status
            });

            // CẬP NHẬT NGAY LẬP TỨC
            // 1. Cập nhật local state
            if (status === 'learned') {
                card.progress_status = 'learned';
            } else {
                card.progress_status = 'new';
            }

            // 2. Reload stats (tức thì)
            this.loadStats();

            // 3. Reload sets (tức thì)
            this.loadSets();

            // 4. Cập nhật UI hiện tại trong study modal
            this.updateStudyUI(status);

            // Logic xử lý dựa trên mode
            if (this.studyModeType === 'all') {
                // Chế độ học tất cả: luôn chuyển sang card tiếp theo
                this.currentCardIndex++;

                // Nếu đã hết cards và đang ở mode "all", quay lại đầu
                if (this.currentCardIndex >= this.currentCards.length) {
                    this.currentCardIndex = 0;
                    App.showToast('Starting from the beginning', 'info');
                }
            } else {
                // Chế độ chỉ học chưa thuộc
                if (status === 'learned') {
                    // Đánh dấu "đã thuộc" → remove card khỏi danh sách
                    this.currentCards.splice(this.currentCardIndex, 1);

                    // Nếu đã hết card, kết thúc session
                    if (this.currentCards.length === 0) {
                        this.endStudySession();
                        return;
                    }

                    // Nếu current index vượt quá, quay lại card trước
                    if (this.currentCardIndex >= this.currentCards.length) {
                        this.currentCardIndex = this.currentCards.length - 1;
                    }
                } else {
                    // Đánh dấu "chưa thuộc" → chuyển sang card tiếp theo
                    this.currentCardIndex++;

                    // Kiểm tra nếu đã hết cards
                    if (this.currentCardIndex >= this.currentCards.length) {
                        this.endStudySession();
                        return;
                    }
                }
            }

            // Hiển thị card tiếp theo
            this.showCard();

        } catch (error) {
            console.error('Failed to update progress:', error);
        }
    }

    // Thêm hàm updateStudyUI
    updateStudyUI(status) {
        // Cập nhật trạng thái hiển thị trong study modal
        const studyCard = document.getElementById('current-study-card');
        if (studyCard) {
            // Tìm các phần tử cần cập nhật
            const frontContent = studyCard.querySelector('.study-card-front .card-content');
            const backContent = studyCard.querySelector('.study-card-back .card-content');

            if (frontContent && backContent) {
                // Cập nhật trạng thái
                const statusHtml = status === 'learned' ?
                    '<div class="text-sm text-text-secondary mt-2 status-label">(Đã thuộc)</div>' :
                    '<div class="text-sm text-text-secondary mt-2 status-label">(Chưa thuộc)</div>';

                // Xóa các nhãn cũ
                frontContent.querySelectorAll('.status-label').forEach(el => el.remove());
                backContent.querySelectorAll('.status-label').forEach(el => el.remove());

                // Thêm nhãn mới
                frontContent.insertAdjacentHTML('beforeend', statusHtml);
                backContent.insertAdjacentHTML('beforeend', statusHtml.replace('text-text-secondary', 'text-white/80'));
            }
        }
    }

    endStudySession() {
        this.studyMode = false;
        this.studyModeType = null;

        // Đóng modal
        this.closeAllModals();

        // Hiển thị thông báo
        App.showToast('Study session completed!', 'success');

        // Reload data ngay lập tức
        this.loadStats(false); // false = không show loading
        this.loadSets(false);

        // Reload lại study sets nếu đang ở tab study
        const activeTab = document.querySelector('.tab-btn.active');
        if (activeTab && activeTab.dataset.tab === 'study') {
            this.loadStudySets();
        }
    }

    openCardModal(setId, cardId = null, question = '', answer = '') {
        // Đóng modal view set trước (nếu có)
        const viewSetModal = document.querySelector('.modal[data-set-id]');
        if (viewSetModal) {
            viewSetModal.style.display = 'none';
        }

        // Kiểm tra nếu modal đã tồn tại
        const existingModal = document.getElementById('card-modal');
        if (existingModal) {
            existingModal.remove();
        }

        // Tạo modal mới
        const modal = document.createElement('div');
        modal.id = 'card-modal';
        modal.className = 'modal';
        modal.dataset.dynamic = "true";

        const modalTitle = cardId ? 'Edit Card' : 'Add New Card';

        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">${modalTitle}</h3>
                    <button class="modal-close">x</button>
                </div>
                <div class="modal-body">
                    <form id="card-form">
                        <input type="hidden" id="card-id" value="${cardId || ''}">
                        <input type="hidden" id="card-set-id" value="${setId || ''}">
                        <div class="form-group">
                            <label class="form-label">Question</label>
                            <textarea id="card-question" class="form-control" rows="3" 
                                    placeholder="Enter the question or term" required>${question}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Answer</label>
                            <textarea id="card-answer" class="form-control" rows="3" 
                                    placeholder="Enter the answer or definition" required>${answer}</textarea>
                        </div>
                        <div class="flex gap-6 justify-end space-x-3 mt-6">
                            <button type="button" class="btn btn-secondary" id="cancel-card-btn">Cancel</button>
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
        `;

        document.body.appendChild(modal);

        // Show modal
        modal.style.display = 'flex';
        document.getElementById('card-question')?.focus();

        // Add event listeners
        const closeBtn = modal.querySelector('.modal-close');
        const cancelBtn = modal.querySelector('#cancel-card-btn');

        const closeModal = () => {
            modal.remove();
            // Hiện lại modal view set (nếu có)
            if (viewSetModal) {
                viewSetModal.style.display = 'flex';
            }
        };

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        // Close on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Form submission
        const form = modal.querySelector('#card-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveCard();
            });
        }
    }

    async saveCard() {
        const cardId = document.getElementById('card-id');
        const setId = document.getElementById('card-set-id');
        const question = document.getElementById('card-question');
        const answer = document.getElementById('card-answer');
        const saveBtn = document.getElementById('save-card-btn');

        // Kiểm tra tất cả các element
        if (!cardId || !setId || !question || !answer || !saveBtn) {
            console.error('Card form elements not found');
            App.showToast('Form error: elements missing', 'error');
            return;
        }

        const cardIdValue = cardId.value;
        const setIdValue = setId.value;
        const questionValue = question.value.trim();
        const answerValue = answer.value.trim();

        if (!questionValue || !answerValue) {
            App.showToast('Please fill in all fields', 'error');
            return;
        }

        // Tìm các element con trong saveBtn
        const saveText = saveBtn.querySelector('.save-text');
        const spinnerText = saveBtn.querySelector('.spinner-text');

        // Show loading (nếu có spinner)
        if (saveText && spinnerText) {
            saveText.style.display = 'none';
            spinnerText.style.display = 'flex';
        }
        saveBtn.disabled = true;

        try {
            const action = cardIdValue ? 'update_card' : 'create_card';
            const data = cardIdValue ?
                { card_id: cardIdValue, question: questionValue, answer: answerValue } :
                { set_id: setIdValue, question: questionValue, answer: answerValue };

            await this.apiCall(action, data);

            App.showToast('Card saved successfully', 'success');
            this.closeAllModals();

            // Reload current view nếu có setId
            if (setIdValue) {
                // Đóng modal hiện tại trước
                const currentModal = document.querySelector('.modal[data-dynamic="true"]');
                if (currentModal) {
                    currentModal.remove();
                }

                // Reload cards
                this.viewSet(setIdValue, '');
            }
        } catch (error) {
            console.error('Failed to save card:', error);
        } finally {
            // Reset button (nếu có spinner)
            if (saveText && spinnerText) {
                saveText.style.display = 'block';
                spinnerText.style.display = 'none';
            }
            saveBtn.disabled = false;
        }
    }

    editCard(cardId, question, answer, setId) {
        // Decode HTML entities
        const decodedQuestion = this.decodeHtml(question);
        const decodedAnswer = this.decodeHtml(answer);

        this.openCardModal(setId, cardId, decodedQuestion, decodedAnswer);
    }
    async deleteCard(cardId) {
        if (!confirm('Are you sure you want to delete this card?')) {
            return;
        }

        try {
            await this.apiCall('delete_card', { card_id: cardId });
            App.showToast('Card deleted successfully', 'success');

            // Reload UI
            this.loadStats();
            this.loadSets();

            // Tìm modal view set đang mở và reload cards
            const viewSetModal = document.querySelector('.modal[data-set-id]');
            if (viewSetModal) {
                const setId = viewSetModal.dataset.setId;
                const title = viewSetModal.querySelector('.modal-title').textContent
                    .replace(/ \(\d+ cards\)$/, ''); // Loại bỏ "(X cards)"

                // Reload cards trong modal
                const result = await this.apiCall('get_cards', { set_id: setId }, 'GET');
                if (result && result.cards) {
                    // Cập nhật title với số cards mới
                    const titleElement = viewSetModal.querySelector('.modal-title');
                    if (titleElement) {
                        titleElement.textContent = `${title} (${result.cards.length} cards)`;
                    }

                    // Cập nhật danh sách cards
                    const cardsContainer = viewSetModal.querySelector('.cards-list');
                    if (cardsContainer && result.cards.length > 0) {
                        cardsContainer.innerHTML = result.cards.map(card => `
                            <div class="card">
                                <div class="card-question">${this.escapeHtml(card.question)}</div>
                                <div class="card-answer">${this.escapeHtml(card.answer)}</div>
                                <div class="card-actions">
                                    <button class="btn btn-sm btn-secondary edit-card-btn" 
                                            data-card-id="${card.card_id}"
                                            data-question="${this.escapeHtml(card.question)}"
                                            data-answer="${this.escapeHtml(card.answer)}"
                                            data-set-id="${setId}">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-card-btn" 
                                            data-card-id="${card.card_id}">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        `).join('');

                        // Re-attach event listeners
                        this.attachCardEventListeners(viewSetModal, setId);
                    } else if (cardsContainer) {
                        // Hiển thị empty state
                        cardsContainer.innerHTML = `
                            <div class="empty-state py-8">
                                <div class="empty-state-icon">📝</div>
                                <h3 class="empty-state-title">No cards yet</h3>
                                <p class="empty-state-description">
                                    Add your first card to this set
                                </p>
                            </div>
                        `;
                    }
                }
            }

        } catch (error) {
            console.error('Failed to delete card:', error);
            App.showToast('Failed to delete card', 'error');
        }
    }
    // Thêm hàm attach event listeners
    attachCardEventListeners(modal, setId) {
        // Edit card buttons
        const editButtons = modal.querySelectorAll('.edit-card-btn');
        editButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const cardId = e.target.dataset.cardId;
                const question = e.target.dataset.question;
                const answer = e.target.dataset.answer;
                this.editCard(cardId, question, answer, setId);
            });
        });

        // Delete card buttons
        const deleteButtons = modal.querySelectorAll('.delete-card-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const cardId = e.target.dataset.cardId;
                this.deleteCard(cardId);
            });
        });
    }
    closeAllModals() {
        // Đóng modal có sẵn
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });

        // Xóa modal động
        document.querySelectorAll('.modal[data-dynamic="true"]').forEach(modal => {
            modal.remove();
        });

        // Remove event listeners
        document.removeEventListener('keydown', this.handleStudyKeydown);
    }
    escapeHtml(text) {
        if (!text) return '';

        const div = document.createElement('div');
        div.textContent = text;

        // Xử lý các ký tự đặc biệt trong HTML attribute
        let escaped = div.innerHTML;

        // Escape quotes cho attribute
        escaped = escaped.replace(/"/g, '&quot;');
        escaped = escaped.replace(/'/g, '&#x27;');

        return escaped;
    }

    decodeHtml(html) {
        if (!html) return '';

        const txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    }

    promptStudyAll(setId, title) {
        // Tạo modal hỏi
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header mb-3">
                    <h3 class="modal-title">🎉 All Cards Learned!</h3>
                    <button class="modal-close">x</button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-6">
                        <div class="text-5xl mb-4">✅</div>
                        <h4 class="text-xl font-semibold mb-2">Great job!</h4>
                        <p class="text-text-secondary mb-6">
                            You've learned all cards in "<strong>${this.escapeHtml(title)}</strong>".
                            Do you want to study all cards again?
                        </p>
                        <div class="flex justify-center space-x-4">
                            <button class="btn btn-secondary" id="cancel-study-all">
                                Cancel
                            </button>
                            <button class="btn btn-primary" id="study-all-cards">
                                Study All Cards
                            </button>
                            <button class="btn btn-warning" id="reset-progress">
                                Reset Progress
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.display = 'flex';

        // Event listeners
        modal.querySelector('.modal-close').addEventListener('click', () => {
            modal.remove();
        });

        modal.querySelector('#cancel-study-all').addEventListener('click', () => {
            modal.remove();
        });

        modal.querySelector('#study-all-cards').addEventListener('click', () => {
            modal.remove();
            this.studyAllSet(setId, title); // Học tất cả
        });

        modal.querySelector('#reset-progress').addEventListener('click', async () => {
            if (confirm('Reset all cards to "unlearned" status?')) {
                try {
                    await this.apiCall('reset_progress', { set_id: setId });
                    App.showToast('All cards reset to unlearned', 'success');
                    modal.remove();
                    this.studySet(setId, title, false); // Bắt đầu học lại
                } catch (error) {
                    console.error('Failed to reset progress:', error);
                }
            }
        });

        // Close on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    // Hàm học tất cả cards
    async studyAllSet(setId, title) {
        try {
            // Lấy tất cả cards (kể cả đã thuộc)
            const result = await this.apiCall('get_unlearned_cards', {
                set_id: setId,
                include_all: '1'
            }, 'GET');

            if (result.cards && result.cards.length > 0) {
                this.startStudySession(result.cards, title, setId, 'all');
            } else {
                App.showToast('No cards found in this set', 'warning');
            }
        } catch (error) {
            console.error('Failed to load all cards:', error);
            App.showToast('Failed to load cards', 'error');
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.flashcards = new FlashcardManager();
});