// ========================================
// FLOATING CHAT - BacLab
// Defensive, Conflict-Free Implementation
// ========================================

(function() {
    'use strict';
    
    // Prevent conflicts with other scripts
    const originalConsoleError = console.error;
    
    // State
    let currentConversation = null;
    let currentTab = 'conversations';
    let messagesPollingInterval = null;
    let isInitialized = false;
    
    // DOM Elements - with null checks
    let chatToggle, chatPanel, chatClose, chatContent, conversationsView, messagesView;
    let chatConversationsList, chatMessagesList, chatMessageInput, chatSendBtn;
    let chatBackBtn, chatGroupName, chatGroupMembers, tabButtons;
    
    // Safe DOM element getter
    function safeGetElement(id) {
        try {
            return document.getElementById(id);
        } catch (e) {
            console.warn('Element not found:', id);
            return null;
        }
    }
    
    // Initialize DOM elements
    function initializeElements() {
        chatToggle = safeGetElement('floatingChatToggle');
        chatPanel = safeGetElement('floatingChatPanel');
        chatClose = safeGetElement('floatingChatClose');
        chatContent = safeGetElement('floatingChatContent');
        conversationsView = safeGetElement('conversationsView');
        messagesView = safeGetElement('messagesView');
        chatConversationsList = safeGetElement('chatConversationsList');
        chatMessagesList = safeGetElement('chatMessagesList');
        chatMessageInput = safeGetElement('chatMessageInput');
        chatSendBtn = safeGetElement('chatSendBtn');
        chatBackBtn = safeGetElement('chatBackBtn');
        chatGroupName = safeGetElement('chatGroupName');
        chatGroupMembers = safeGetElement('chatGroupMembers');
        tabButtons = document.querySelectorAll('.chat-tab-btn');
        
        return chatToggle && chatPanel;
    }
    
    // Initialize
    function init() {
        if (isInitialized) return;
        
        if (!initializeElements()) {
            console.warn('Floating chat elements not found, retrying...');
            setTimeout(init, 1000);
            return;
        }
        
        isInitialized = true;
        
        // Event Listeners with error handling
        try {
            chatToggle.addEventListener('click', toggleChat);
            if (chatClose) chatClose.addEventListener('click', closeChat);
            if (chatSendBtn) chatSendBtn.addEventListener('click', sendMessage);
            if (chatBackBtn) chatBackBtn.addEventListener('click', goBackToConversations);
            
            if (chatMessageInput) {
                chatMessageInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        sendMessage();
                    }
                });
            }
            
            // Tab switching
            if (tabButtons && tabButtons.length > 0) {
                tabButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        switchTab(this.dataset.tab);
                    });
                });
            }
            
            // Load initial conversations
            loadConversations();
            
            console.log('Floating chat initialized successfully');
        } catch (error) {
            console.error('Error initializing floating chat:', error);
        }
    }
    
    // Toggle Chat Panel
    function toggleChat() {
        try {
            if (!chatPanel || !chatToggle) return;
            
            const isActive = chatPanel.classList.contains('active');
            
            if (isActive) {
                chatPanel.classList.remove('active');
                chatToggle.classList.remove('active');
                stopMessagesPolling();
            } else {
                chatPanel.classList.add('active');
                chatToggle.classList.add('active');
                loadConversations();
                startMessagesPolling();
            }
        } catch (error) {
            console.error('Error toggling chat:', error);
        }
    }
    
    // Close Chat Panel
    function closeChat() {
        try {
            if (!chatPanel || !chatToggle) return;
            
            chatPanel.classList.remove('active');
            chatToggle.classList.remove('active');
            stopMessagesPolling();
        } catch (error) {
            console.error('Error closing chat:', error);
        }
    }
    
    // Switch Tab
    function switchTab(tab) {
        currentTab = tab;
        
        tabButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tab);
        });
        
        if (tab === 'conversations') {
            loadConversations();
        } else {
            loadRecentMessages();
        }
    }
    
    // Load Conversations (User's Groups)
    function loadConversations() {
        fetch('/api/chat/conversations')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderConversations(data.conversations);
                } else {
                    renderEmptyState('Erreur lors du chargement des conversations');
                }
            })
            .catch(error => {
                console.error('Error loading conversations:', error);
                renderEmptyState('Erreur de connexion');
            });
    }
    
    // Load Recent Messages
    function loadRecentMessages() {
        fetch('/api/chat/recent')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderConversations(data.messages);
                } else {
                    renderEmptyState('Erreur lors du chargement des messages');
                }
            })
            .catch(error => {
                console.error('Error loading recent messages:', error);
                renderEmptyState('Erreur de connexion');
            });
    }
    
    // Render Conversations List
    function renderConversations(conversations) {
        if (!conversations || conversations.length === 0) {
            chatConversationsList.innerHTML = `
                <li class="chat-empty-state">
                    <i class="fas fa-comments"></i>
                    <h5>Aucune conversation</h5>
                    <p>Rejoignez un groupe pour commencer à discuter</p>
                </li>
            `;
            return;
        }
        
        let html = '';
        conversations.forEach(conv => {
            const initials = getInitials(conv.nom);
            const lastMessage = conv.dernierMessage || 'Aucun message';
            const timeAgo = conv.timeAgo || '';
            const unread = conv.unreadCount || 0;
            
            html += `
                <li class="chat-conversation-item" data-id="${conv.id}" onclick="window.openChatConversation(${conv.id}, '${conv.nom.replace(/'/g, "\\'")}', ${conv.membreCount || 0})">
                    <div class="chat-conversation-avatar">
                        ${initials}
                    </div>
                    <div class="chat-conversation-info">
                        <div class="chat-conversation-name">
                            <span>${conv.nom}</span>
                            <span class="chat-conversation-time">${timeAgo}</span>
                        </div>
                        <div class="chat-conversation-preview">${lastMessage}</div>
                    </div>
                    ${unread > 0 ? `<span class="chat-conversation-unread">${unread}</span>` : ''}
                </li>
            `;
        });
        
        chatConversationsList.innerHTML = html;
    }
    
    // Render Empty State
    function renderEmptyState(message) {
        chatConversationsList.innerHTML = `
            <li class="chat-empty-state">
                <i class="fas fa-comments"></i>
                <h5>${message}</h5>
                <p>Rejoignez un groupe pour commencer à discuter</p>
            </li>
        `;
    }
    
    // Open Chat Conversation
    window.openChatConversation = function(groupId, groupName, memberCount) {
        currentConversation = {
            id: groupId,
            nom: groupName,
            membreCount: memberCount
        };
        
        // Update header
        chatGroupName.textContent = groupName;
        chatGroupMembers.textContent = memberCount + ' membres';
        
        // Switch views
        conversationsView.style.display = 'none';
        messagesView.style.display = 'flex';
        
        // Load messages
        loadMessages(groupId);
        
        // Start polling for new messages
        startMessagesPolling();
    };
    
    // Load Messages for a Group
    function loadMessages(groupId) {
        chatMessagesList.innerHTML = `
            <div class="chat-loading">
                <div class="chat-spinner"></div>
            </div>
        `;
        
        fetch(`/api/chat/messages/${groupId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderMessages(data.messages);
                } else {
                    chatMessagesList.innerHTML = `
                        <div class="chat-empty-state">
                            <i class="fas fa-exclamation-circle"></i>
                            <h5>Erreur</h5>
                            <p>${data.error || 'Impossible de charger les messages'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                chatMessagesList.innerHTML = `
                    <div class="chat-empty-state">
                        <i class="fas fa-wifi"></i>
                        <h5>Erreur de connexion</h5>
                        <p>Vérifiez votre connexion internet</p>
                    </div>
                `;
            });
    }
    
    // Render Messages
    function renderMessages(messages) {
        if (!messages || messages.length === 0) {
            chatMessagesList.innerHTML = `
                <div class="chat-empty-state">
                    <i class="fas fa-comments"></i>
                    <h5>Premier message</h5>
                    <p>Soyez le premier à envoyer un message dans ce groupe!</p>
                </div>
            `;
            return;
        }
        
        // Get current user ID from window variable (set by Twig template)
        const currentUserId = window.currentUserId || 0;
        
        let html = '';
        messages.forEach(msg => {
            const isSent = msg.expediteurId === currentUserId;
            const time = formatTime(msg.createdAt);
            
            html += `
                <div class="chat-message ${isSent ? 'sent' : 'received'}">
                    <div class="chat-message-bubble">${escapeHtml(msg.contenu)}</div>
                    <div class="chat-message-time">${time}</div>
                </div>
            `;
        });
        
        chatMessagesList.innerHTML = html;
        
        // Scroll to bottom
        chatMessagesList.scrollTop = chatMessagesList.scrollHeight;
    }
    
    // Send Message
    function sendMessage() {
        if (!currentConversation) return;
        
        const contenu = chatMessageInput.value.trim();
        if (!contenu) return;
        
        // Disable input
        chatMessageInput.disabled = true;
        chatSendBtn.disabled = true;
        
        const formData = new FormData();
        formData.append('contenu', contenu);
        formData.append('typeMessage', 'TEXTE');
        
        fetch(`/api/chat/send/${currentConversation.id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                chatMessageInput.value = '';
                // Reload messages
                loadMessages(currentConversation.id);
            } else {
                alert(data.error || 'Erreur lors de l\'envoi du message');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Erreur de connexion');
        })
        .finally(() => {
            chatMessageInput.disabled = false;
            chatSendBtn.disabled = false;
            chatMessageInput.focus();
        });
    }
    
    // Go Back to Conversations
    function goBackToConversations() {
        currentConversation = null;
        messagesView.style.display = 'none';
        conversationsView.style.display = 'block';
        stopMessagesPolling();
    }
    
    // Start Polling for New Messages
    function startMessagesPolling() {
        stopMessagesPolling(); // Clear any existing interval
        
        if (currentConversation) {
            messagesPollingInterval = setInterval(() => {
                loadMessages(currentConversation.id);
            }, 5000); // Poll every 5 seconds
        }
    }
    
    // Stop Polling
    function stopMessagesPolling() {
        if (messagesPollingInterval) {
            clearInterval(messagesPollingInterval);
            messagesPollingInterval = null;
        }
    }
    
    // Helper Functions
    function getInitials(name) {
        if (!name) return '?';
        const words = name.split(' ');
        if (words.length >= 2) {
            return (words[0][0] + words[1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    }
    
    function formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        if (diff < 60000) return 'À l\'instant';
        if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
        if (diff < 86400000) return Math.floor(diff / 3600000) + 'h';
        if (diff < 604800000) return Math.floor(diff / 86400000) + 'j';
        
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }
    
    // Initialize when DOM is ready - with multiple fallbacks
    function initializeWhenReady() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            // Fallback in case DOMContentLoaded already fired
            setTimeout(init, 100);
        } else {
            init();
        }
        
        // Additional fallback for slow-loading pages
        setTimeout(function() {
            if (!isInitialized) {
                init();
            }
        }, 2000);
    }
    
    // Start initialization
    initializeWhenReady();
    
    // Expose for debugging
    window.FloatingChat = {
        init: init,
        toggle: toggleChat,
        close: closeChat,
        isInitialized: function() { return isInitialized; }
    };
})();
