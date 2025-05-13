// Function to show notifications
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    
    // Add classes based on notification type
    let bgColor, textColor, borderColor, icon;
    
    if (type === 'success') {
        bgColor = 'bg-green-50';
        textColor = 'text-green-800';
        borderColor = 'border-green-500';
        icon = '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    } else {
        bgColor = 'bg-red-50';
        textColor = 'text-red-800';
        borderColor = 'border-red-500';
        icon = '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    }
    
    // Add classes and styles for positioning at bottom right
    notification.className = `${bgColor} ${textColor} border-l-4 ${borderColor} px-4 py-3 rounded-md shadow-lg fixed bottom-6 right-6 z-50 max-w-md backdrop-blur-sm bg-opacity-95`;
    notification.style.transition = 'transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.4s ease';
    notification.style.transform = 'translateY(20px) scale(0.95)';
    notification.style.opacity = '0';
    
    // Create inner content with icon
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-3">
                ${icon}
            </div>
            <div class="flex-grow">
                <p class="font-medium">${message}</p>
            </div>
            <div class="ml-3 flex-shrink-0">
                <button class="notification-close transition-transform hover:rotate-90">
                    <svg class="fill-current h-4 w-4 ${textColor}" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    // Add to DOM
    document.body.appendChild(notification);
    
    // Add close event listener
    notification.querySelector('.notification-close').addEventListener('click', function() {
        hideNotification(notification);
    });
    
    // Show notification with animation
    setTimeout(() => {
        notification.style.transform = 'translateY(0) scale(1)';
        notification.style.opacity = '1';
    }, 10);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideNotification(notification);
    }, 5000);
    
    return notification;
}

// Function to hide notification
function hideNotification(notification) {
    notification.style.transform = 'translateY(20px) scale(0.95)';
    notification.style.opacity = '0';
    
    // Remove from DOM after animation completes
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 400); // Slightly longer to match our new animation duration
}

// Function to show multiple notifications and stack them nicely
function createNotificationStack() {
    const stack = {
        items: [],
        add(notification) {
            this.items.push(notification);
            this.updatePositions();
        },
        remove(notification) {
            const index = this.items.indexOf(notification);
            if (index > -1) {
                this.items.splice(index, 1);
                this.updatePositions();
            }
        },
        updatePositions() {
            let offset = 0;
            for (let i = this.items.length - 1; i >= 0; i--) {
                const item = this.items[i];
                item.style.bottom = `${offset + 24}px`; // 6rem = 24px
                offset += item.offsetHeight + 12; // Add spacing between notifications
            }
        }
    };
    return stack;
}

// Initialize notification stack
const notificationStack = createNotificationStack();

// Override the showNotification function to use our stack
const originalShowNotification = showNotification;
showNotification = function(message, type = 'success') {
    const notification = originalShowNotification(message, type);
    notificationStack.add(notification);
    
    // When notification is removed, also remove from stack
    const originalHideNotification = hideNotification;
    hideNotification = function(notif) {
        notificationStack.remove(notif);
        originalHideNotification(notif);
    };
    
    return notification;
};

// Check for session messages on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get message elements if they exist
    const successMsg = document.getElementById('success-message');
    const errorMsg = document.getElementById('error-message');
    
    // Show success notification
    if (successMsg) {
        showNotification(successMsg.textContent, 'success');
        // Remove the original element so it doesn't take up space
        successMsg.parentNode.removeChild(successMsg);
    }
    
    // Show error notification
    if (errorMsg) {
        showNotification(errorMsg.textContent, 'error');
        // Remove the original element so it doesn't take up space
        errorMsg.parentNode.removeChild(errorMsg);
    }
    
    // Convert the flash messages from PHP into our dynamic notifications
    if (document.querySelector('[role="alert"]')) {
        document.querySelectorAll('[role="alert"]').forEach(alert => {
            const message = alert.querySelector('span:not([class*="absolute"])').textContent;
            const type = alert.classList.contains('bg-green-100') ? 'success' : 'error';
            
            // Show as notification
            showNotification(message, type);
            
            // Remove the original alert
            alert.style.display = 'none';
        });
    }
});
