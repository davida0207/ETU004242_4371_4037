<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Modern Bootstrap 5 Admin Template - Messages">
    <meta name="keywords" content="bootstrap, admin, dashboard, messages, chat">
    <meta name="author" content="Bootstrap Admin Template">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Messages - Modern Bootstrap Admin">
    <meta property="og:description" content="Real-time communication center">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/assets/favicon-CvUZKS4z.svg">
    <link rel="icon" type="image/png" href="/assets/favicon-B_cwPWBd.png">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Title -->
    <title>Messages - Modern Bootstrap Admin</title>

    <!-- Theme Color -->
    <meta name="theme-color" content="#6366f1">

    <!-- PWA Manifest + Bundled Assets -->
    <link rel="manifest" href="/assets/manifest-DTaoG9pG.json">
    <script type="module" crossorigin src="/assets/vendor-bootstrap-C9iorZI5.js"></script>
    <script type="module" crossorigin src="/assets/vendor-charts-DGwYAWel.js"></script>
    <script type="module" crossorigin src="/assets/vendor-ui-CflGdlft.js"></script>
    <script type="module" crossorigin src="/assets/main-DwHigVru.js"></script>
    <link rel="stylesheet" crossorigin href="/assets/main-QD_VOj1Y.css">
    <link rel="stylesheet" href="/css/messages-whatsapp.css">
</head>

<body data-page="messages" class="admin-layout">
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="admin-wrapper" id="admin-wrapper">

        <?php $activePage = 'messages'; include __DIR__ . '/partials/header.php'; ?>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="container-fluid p-4 p-lg-4">
                    
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h1 class="h3 mb-0">Messages</h1>
                            <p class="text-muted mb-0">Real-time communication center</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary d-lg-none" @click="toggleSidebar()">
                                <i class="bi bi-list me-2"></i>Conversations
                            </button>
                            <button type="button" class="btn btn-outline-secondary" @click="markAllRead()">
                                <i class="bi bi-check-all me-2"></i>Mark All Read
                            </button>
                            <button type="button" class="btn btn-primary" @click="newConversation()">
                                <i class="bi bi-plus-lg me-2"></i>New Message
                            </button>
                        </div>
                    </div>

                    <!-- Messages Container -->
                    <div x-data="messagesComponent" x-init="init()" class="messages-container">
                        <div class="messages-layout">
                            
                            <!-- Conversations Sidebar -->
                            <div class="messages-sidebar" :class="{ 'mobile-show': sidebarVisible }">
                                <!-- Sidebar Header -->
                                <div class="messages-header">
                                    <h5 class="header-title mb-0">Messages</h5>
                                    <div class="d-flex gap-2 mt-3">
                                        <div class="search-container flex-grow-1">
                                            <input type="search" 
                                                   class="form-control" 
                                                   placeholder="Search conversations..."
                                                   x-model="searchQuery"
                                                   @input="filterConversations()">
                                            <i class="bi bi-search search-icon"></i>
                                        </div>
                                        <button class="btn btn-primary btn-sm" @click="newConversation()" title="New Message">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Conversations List -->
                                <div class="conversations-list">
                                    <!-- Users list (New Message) -->
                                    <div x-show="showUserPicker" class="px-3 pt-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="mb-0">Users</h6>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="showUserPicker = false">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        <div class="search-container mb-2">
                                            <input type="search"
                                                   class="form-control"
                                                   placeholder="Search users..."
                                                   x-model="userSearchQuery"
                                                   @input="filterUsers()">
                                            <i class="bi bi-search search-icon"></i>
                                        </div>
                                    </div>

                                    <div x-show="showUserPicker">
                                        <template x-for="user in filteredUsers" :key="user.id">
                                            <a href="#" class="conversation-item"
                                               @click.prevent="startConversation(user)">
                                                <div class="conversation-avatar">
                                                    <img src="/assets/images/avatar-placeholder.svg" alt="User">
                                                </div>
                                                <div class="conversation-info">
                                                    <div class="conversation-header">
                                                        <h6 class="conversation-name" x-text="`${user.nom} ${user.prenom || ''}`.trim()"></h6>
                                                    </div>
                                                    <p class="conversation-preview" x-text="user.email"></p>
                                                </div>
                                            </a>
                                        </template>

                                        <div x-show="filteredUsers.length === 0" class="empty-conversations">
                                            <i class="bi bi-people"></i>
                                            <p>No users found</p>
                                        </div>
                                    </div>

                                    <template x-for="conversation in filteredConversations" :key="conversation.id">
                                        <a href="#" class="conversation-item" 
                                           :class="{ 
                                               'active': selectedConversation?.id === conversation.id,
                                               'unread': conversation.unread > 0 
                                           }"
                                           @click.prevent="selectConversation(conversation)">
                                            <div class="conversation-avatar">
                                                <img :src="conversation.avatar" 
                                                     :alt="conversation.name"
                                                     :class="{ 'online': conversation.online }">
                                                <div class="online-indicator" x-show="conversation.online"></div>
                                            </div>
                                            <div class="conversation-info">
                                                <div class="conversation-header">
                                                    <h6 class="conversation-name" x-text="conversation.name"></h6>
                                                    <span class="conversation-time" x-text="conversation.lastMessageTime"></span>
                                                </div>
                                                <p class="conversation-preview" x-text="conversation.lastMessage"></p>
                                                <div class="conversation-footer">
                                                    <span class="conversation-type" x-text="conversation.type"></span>
                                                    <span class="unread-badge" 
                                                          x-show="conversation.unread > 0" 
                                                          x-text="conversation.unread"></span>
                                                </div>
                                            </div>
                                        </a>
                                    </template>
                                    
                                    <!-- Empty state for conversations -->
                                    <div x-show="filteredConversations.length === 0" class="empty-conversations">
                                        <i class="bi bi-chat-dots"></i>
                                        <p>No conversations found</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Chat Area -->
                            <div class="chat-area">
                                <!-- Active Chat -->
                                <div class="active-chat" x-show="selectedConversation">
                                    <!-- Chat Header -->
                                    <div class="chat-header">
                                        <div class="chat-user-info">
                                            <button class="btn btn-link d-lg-none me-2 p-0" @click="sidebarVisible = !sidebarVisible">
                                                <i class="bi bi-arrow-left fs-5"></i>
                                            </button>
                                            <div class="chat-avatar-container">
                                                <img :src="selectedConversation?.avatar" 
                                                     class="chat-avatar"
                                                     :alt="selectedConversation?.name">
                                                <div class="online-indicator" x-show="selectedConversation?.online"></div>
                                            </div>
                                            <div class="chat-details">
                                                <h6 class="chat-name" x-text="selectedConversation?.name"></h6>
                                                <p class="chat-status" x-show="selectedConversation?.online">● Online</p>
                                                <p class="chat-status" x-show="!selectedConversation?.online" x-text="`Last seen ${selectedConversation?.lastSeen}`"></p>
                                            </div>
                                        </div>
                                        <div class="chat-actions">
                                            <button class="btn" @click="videoCall()" title="Video Call">
                                                <i class="bi bi-camera-video"></i>
                                            </button>
                                            <button class="btn" @click="voiceCall()" title="Voice Call">
                                                <i class="bi bi-telephone"></i>
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn dropdown-toggle" data-bs-toggle="dropdown" title="More Options">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#" @click.prevent="muteConversation()">
                                                        <i class="bi bi-bell-slash me-2"></i>Mute notifications
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" @click.prevent="archiveConversation()">
                                                        <i class="bi bi-archive me-2"></i>Archive chat
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" @click.prevent="deleteConversation()">
                                                        <i class="bi bi-trash me-2"></i>Delete chat
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Messages Area -->
                                    <div class="chat-messages" id="chatMessages">
                                        <!-- Date Separator -->
                                        <div class="date-separator">
                                            <span class="date-label">Today</span>
                                        </div>
                                        
                                        <!-- Messages -->
                                        <div class="message-group">
                                            <template x-for="message in currentMessages" :key="message.id">
                                                <div class="message" :class="{ 'own-message': message.sent }">
                                                    <img x-show="!message.sent" 
                                                         :src="selectedConversation?.avatar" 
                                                         class="message-avatar" 
                                                         :alt="selectedConversation?.name">
                                                    <div class="message-bubble">
                                                        <div class="message-content">
                                                            <p x-text="message.text"></p>
                                                        </div>
                                                        <div class="message-info">
                                                            <span class="message-time" x-text="message.time"></span>
                                                            <span x-show="message.sent" class="message-status">
                                                                <i class="bi bi-check" x-show="message.status === 'sent'"></i>
                                                                <i class="bi bi-check-all" x-show="message.status === 'delivered'" style="opacity:.7"></i>
                                                                <span x-show="message.status === 'read'" class="seen-label">Seen</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <!-- Typing Indicator -->
                                        <div class="typing-indicator" x-show="isTyping">
                                            <img :src="selectedConversation?.avatar" 
                                                 class="typing-avatar" 
                                                 :alt="selectedConversation?.name">
                                            <div class="typing-content">
                                                <div class="typing-dots">
                                                    <div class="dot"></div>
                                                    <div class="dot"></div>
                                                    <div class="dot"></div>
                                                </div>
                                                <span class="typing-text">typing...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Message Input -->
                                    <div class="chat-input">
                                        <div class="input-container">
                                            <div class="input-actions">
                                                <button class="btn" @click="toggleAttachment()" title="Attach file">
                                                    <i class="bi bi-paperclip"></i>
                                                </button>
                                            </div>
                                            <div class="message-input">
                                                <textarea class="form-control" 
                                                          placeholder="Type a message..." 
                                                          rows="1"
                                                          x-model="newMessage"
                                                          @keydown.enter.prevent="sendMessage()"
                                                          @input="handleTyping(); autoResize($event)"
                                                          style="resize: none;"></textarea>
                                            </div>
                                            <div class="input-actions">
                                                <button class="btn" @click="toggleEmojiPicker()" title="Add emoji">
                                                    <i class="bi bi-emoji-smile"></i>
                                                </button>
                                                <button class="btn btn-primary" @click="sendMessage()" :disabled="!newMessage.trim()" title="Send message">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Emoji Picker -->
                                        <div class="emoji-picker" x-show="showEmojiPicker" x-transition>
                                            <div class="emoji-grid">
                                                <template x-for="emoji in emojis" :key="emoji">
                                                    <button class="emoji-btn" @click="addEmoji(emoji)" x-text="emoji"></button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Empty Chat State -->
                                <div class="empty-chat" x-show="!selectedConversation">
                                    <div class="empty-icon">
                                        <i class="bi bi-chat-dots"></i>
                                    </div>
                                    <h5 class="empty-text">Select a conversation to start messaging</h5>
                                    <p class="text-muted mb-4">Choose from your existing conversations or start a new one</p>
                                    <button class="btn btn-primary" @click="newConversation()">
                                        <i class="bi bi-plus-lg me-2"></i>Start New Conversation
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

            </div>
        </main>

        <?php include __DIR__ . '/partials/footer.php'; ?>

    </div> <!-- /.admin-wrapper -->

</body>
</html>