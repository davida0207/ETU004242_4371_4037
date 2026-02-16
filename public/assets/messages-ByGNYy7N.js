import { m as i } from "./vendor-ui-CflGdlft.js";
document.addEventListener("alpine:init", () => {
    i.data("messagesComponent", () => ({
        sidebarVisible: !1,
        searchQuery: "",
        selectedConversation: null,
        newMessage: "",
        showEmojiPicker: !1,
        isTyping: !1,
        conversations: [],
        filteredConversations: [],
        currentMessages: [],
        users: [],
        filteredUsers: [],
        userSearchQuery: "",
        showUserPicker: !1,
        emojis: ["😀", "😃", "😄", "😁", "😊", "😍", "🥰", "😘", "👍", "👏", "🎉", "❤️", "🔥", "💯", "😂", "🤔"],
        init() { this.loadUsers().then(() => { this.buildConversationsFromUsers(), this.filterConversations(), window.innerWidth >= 992 && this.conversations.length > 0 && this.selectConversation(this.conversations[0]) }) },
        buildConversationsFromUsers() {
            this.conversations = (this.users || []).map(e => {
                const t = `${e.nom || ""} ${e.prenom || ""}`.trim() || e.email || "User";
                return { id: e.id, name: t, avatar: "/assets/images/avatar-placeholder.svg", type: "User", online: !1, lastMessage: "", lastMessageTime: "", lastSeen: "", unread: 0, receiverId: e.id, messages: [] }
            })
        },
        loadSampleData() { this.conversations = [{ id: 1, name: "John Smith", avatar: "/assets/images/avatar-placeholder.svg", type: "Customer", online: !0, lastMessage: "Thank you for the quick response!", lastMessageTime: "2m ago", lastSeen: "2m ago", unread: 2, messages: [{ id: 1, text: "Hi! I have a question about my recent order.", time: "10:30 AM", sent: !1 }, { id: 2, text: "Hello John! I'd be happy to help you with your order. What seems to be the issue?", time: "10:32 AM", sent: !0 }, { id: 3, text: "I haven't received a tracking number yet, and it's been 3 days since I placed the order.", time: "10:33 AM", sent: !1 }, { id: 4, text: "Let me check that for you right away. Can you please provide your order number?", time: "10:35 AM", sent: !0 }, { id: 5, text: "Sure! It's ORD-2025-001", time: "10:36 AM", sent: !1 }, { id: 6, text: "Perfect! I can see your order here. It was shipped yesterday and the tracking number is TR123456789. You should receive an email with the details shortly.", time: "10:38 AM", sent: !0 }, { id: 7, text: "Thank you for the quick response!", time: "10:40 AM", sent: !1 }, { id: 8, text: "You're very welcome! Is there anything else I can help you with today?", time: "10:42 AM", sent: !0 }, { id: 9, text: "Actually, yes! I was wondering about the return policy for this product.", time: "10:45 AM", sent: !1 }, { id: 10, text: "Great question! You have 30 days from the delivery date to return any item for a full refund. The item just needs to be in its original condition.", time: "10:47 AM", sent: !0 }, { id: 11, text: "That's perfect. And what about exchanges?", time: "10:48 AM", sent: !1 }, { id: 12, text: "Exchanges follow the same 30-day policy. You can exchange for a different size, color, or even a completely different product of equal or lesser value.", time: "10:50 AM", sent: !0 }, { id: 13, text: "Excellent! You've been incredibly helpful. I think I have everything I need now.", time: "10:52 AM", sent: !1 }, { id: 14, text: "I'm so glad I could help! If you have any other questions in the future, please don't hesitate to reach out. Have a wonderful day! 😊", time: "10:54 AM", sent: !0 }, { id: 15, text: "You too! Thanks again for the excellent customer service.", time: "10:55 AM", sent: !1 }] }, { id: 2, name: "Sarah Johnson", avatar: "/assets/images/avatar-placeholder.svg", type: "Team", online: !0, lastMessage: "The new dashboard looks great!", lastMessageTime: "1h ago", lastSeen: "45m ago", unread: 1, messages: [{ id: 1, text: "Hey! Can you review the new dashboard design when you get a chance?", time: "9:15 AM", sent: !1 }, { id: 2, text: "Absolutely! Let me take a look now.", time: "9:18 AM", sent: !0 }, { id: 3, text: "The new dashboard looks great! I love the updated charts and the clean layout.", time: "9:45 AM", sent: !1 }] }, { id: 3, name: "Mike Davis", avatar: "/assets/images/avatar-placeholder.svg", type: "Vendor", online: !1, lastMessage: "I'll get back to you with the pricing.", lastMessageTime: "3h ago", lastSeen: "2h ago", unread: 0, messages: [{ id: 1, text: "Hi Mike! We're looking to place a bulk order. Can you send us a quote?", time: "8:30 AM", sent: !0 }, { id: 2, text: "Sure thing! What quantities are you looking at?", time: "8:45 AM", sent: !1 }, { id: 3, text: "We need about 500 units of the premium package.", time: "8:47 AM", sent: !0 }, { id: 4, text: "I'll get back to you with the pricing.", time: "8:50 AM", sent: !1 }] }, { id: 4, name: "Emily Brown", avatar: "/assets/images/avatar-placeholder.svg", type: "Customer", online: !1, lastMessage: "Perfect, thanks!", lastMessageTime: "1d ago", lastSeen: "18h ago", unread: 0, messages: [{ id: 1, text: "Is there a way to cancel my subscription?", time: "Yesterday 2:30 PM", sent: !1 }, { id: 2, text: "Yes, you can cancel anytime from your account settings. Would you like me to guide you through it?", time: "Yesterday 2:35 PM", sent: !0 }, { id: 3, text: "That would be great, thank you!", time: "Yesterday 2:36 PM", sent: !1 }, { id: 4, text: "Go to Settings > Billing > Cancel Subscription. You'll see a red button at the bottom.", time: "Yesterday 2:37 PM", sent: !0 }, { id: 5, text: "Perfect, thanks!", time: "Yesterday 2:40 PM", sent: !1 }] }, { id: 5, name: "David Wilson", avatar: "/assets/images/avatar-placeholder.svg", type: "Support", online: !0, lastMessage: "The issue has been resolved.", lastMessageTime: "2d ago", lastSeen: "1d ago", unread: 0, messages: [{ id: 1, text: "We've received reports of slow loading times on the dashboard.", time: "2 days ago", sent: !1 }, { id: 2, text: "Thanks for reporting this. I'll investigate right away.", time: "2 days ago", sent: !0 }, { id: 3, text: "The issue has been resolved.", time: "2 days ago", sent: !1 }] }] },
        filterConversations() {
            if (!this.searchQuery.trim()) this.filteredConversations = [...this.conversations];
            else {
                const e = this.searchQuery.toLowerCase();
                this.filteredConversations = this.conversations.filter(t => t.name.toLowerCase().includes(e) || t.lastMessage.toLowerCase().includes(e) || t.type.toLowerCase().includes(e))
            }
        },
        async loadUsers() {
            try {
                const e = await fetch("/api/users", { headers: { Accept: "application/json" } });
                if (!e.ok) throw new Error("users failed: " + e.status);
                const t = await e.json();
                if (!t || !t.ok) throw new Error(t && t.error ? t.error : "users failed");
                this.users = Array.isArray(t.users) ? t.users : [], this.filterUsers()
            } catch (e) {
                this.users = [], this.filteredUsers = [], console.error(e)
            }
        },

        async loadMessagesForConversation(e) {
            try {
                const t = await fetch(`/api/messages/${encodeURIComponent(String(e))}`, { headers: { Accept: "application/json" } });
                if (!t.ok) throw new Error("messages failed: " + t.status);
                const s = await t.json();
                if (!s || !s.ok) throw new Error(s && s.error ? s.error : "messages failed");
                return Array.isArray(s.messages) ? s.messages : []
            } catch (t) {
                return console.error(t), []
            }
        },
        filterUsers() {
            const e = (this.userSearchQuery || "").trim().toLowerCase();
            e ? this.filteredUsers = this.users.filter(t => (`${t.nom||""} ${t.prenom||""} ${t.email||""}`).toLowerCase().includes(e)) : this.filteredUsers = [...this.users]
        },
        async selectConversation(e) {
            this.selectedConversation = e, this.currentMessages = [], e.unread = 0, window.innerWidth < 992 && (this.sidebarVisible = !1);
            const t = await this.loadMessagesForConversation(e.id);
            this.currentMessages = [...t], this.$nextTick(() => { this.scrollToBottom() })
        },
        startConversation(e) {
            const t = `${e.nom || ""} ${e.prenom || ""}`.trim() || e.email || "User";
            const s = { id: e.id, name: t, avatar: "/assets/images/avatar-placeholder.svg", type: "User", online: !1, lastMessage: "", lastMessageTime: "", lastSeen: "", unread: 0, receiverId: e.id, messages: [] };
            const a = this.conversations.find(n => n.id === s.id);
            a ? this.selectConversation(a) : (this.conversations.unshift(s), this.filterConversations(), this.selectConversation(s));
            this.showUserPicker = !1
        },
        async sendMessage() {
            if (!this.newMessage.trim() || !this.selectedConversation) return;
            try {
                const e = this.newMessage.trim();
                const t = new URLSearchParams;
                t.set("receiver_id", String(this.selectedConversation.receiverId || this.selectedConversation.id)), t.set("text", e);
                const s = await fetch("/api/messages/send", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: t.toString() });
                if (!s.ok) throw new Error("send failed: " + s.status);
                const a = await s.json();
                if (!a || !a.ok) throw new Error(a && a.error ? a.error : "send failed");
                const o = a.message || { id: Date.now(), text: e, time: new Date().toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" }), sent: !0 };
                this.currentMessages.push(o), this.selectedConversation.lastMessage = e, this.selectedConversation.lastMessageTime = "now";
                const r = this.conversations.findIndex(n => n.id === this.selectedConversation.id);
                r !== -1 && (this.conversations[r].messages.push(o), this.conversations[r].lastMessage = e, this.conversations[r].lastMessageTime = "now"), this.newMessage = "", this.$nextTick(() => { this.scrollToBottom() })
            } catch (e) {
                this.showNotification("Erreur envoi message", "error"), console.error(e)
            }
        },
        scrollToBottom() {
            const e = document.getElementById("chatMessages");
            e && (e.scrollTop = e.scrollHeight)
        },
        handleTyping() { console.log("User is typing...") },
        autoResize(e) {
            const t = e.target;
            t.style.height = "auto";
            const s = Math.min(t.scrollHeight, 120);
            t.style.height = s + "px"
        },
        toggleSidebar() { this.sidebarVisible = !this.sidebarVisible },
        toggleEmojiPicker() { this.showEmojiPicker = !this.showEmojiPicker },
        addEmoji(e) { this.newMessage += e, this.showEmojiPicker = !1 },
        toggleAttachment() { this.showNotification("File attachment feature would open here", "info") },
        markAllRead() { this.conversations.forEach(e => { e.unread = 0 }), this.showNotification("All conversations marked as read", "success") },
        newConversation() { this.showUserPicker = !0, this.sidebarVisible = !0, this.userSearchQuery = "", this.filterUsers() },
        videoCall() { this.showNotification("Video call would start here", "info") },
        voiceCall() { this.showNotification("Voice call would start here", "info") },
        muteConversation() { this.showNotification("Conversation muted", "success") },
        archiveConversation() { this.selectedConversation && this.showNotification(`${this.selectedConversation.name} archived`, "success") },
        deleteConversation() { this.selectedConversation && confirm(`Delete conversation with ${this.selectedConversation.name}?`) && (this.conversations = this.conversations.filter(e => e.id !== this.selectedConversation.id), this.filterConversations(), this.selectedConversation = null, this.currentMessages = [], this.showNotification("Conversation deleted", "success")) },
        showNotification(e, t = "info") { typeof Swal < "u" ? Swal.fire({ title: e, icon: t === "success" ? "success" : t === "error" ? "error" : "info", toast: !0, position: "top-end", showConfirmButton: !1, timer: 3e3 }) : alert(e) }
    })), i.data("searchComponent", () => ({ query: "", search() { console.log("Searching for:", this.query) } })), i.data("themeSwitch", () => ({ currentTheme: "light", init() { this.currentTheme = localStorage.getItem("theme") || "light", document.documentElement.setAttribute("data-bs-theme", this.currentTheme) }, toggle() { this.currentTheme = this.currentTheme === "light" ? "dark" : "light", document.documentElement.setAttribute("data-bs-theme", this.currentTheme), localStorage.setItem("theme", this.currentTheme) } }))
});