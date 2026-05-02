var notf_img = document.getElementById("notf_img");
var notf_side = document.getElementById("notf_side");
var notf_message = document.getElementById("notf_message");

var notification = document.getElementsByClassName("notification")[0];

let notificationCount = 0; // Counter

function createNotfy(type, message) {
    // Creating a new notification element
    var notification = document.createElement("div");
    notification.classList.add("notification", "background", "fontcorpo");

    var notf_side = document.createElement("div");
    notf_side.classList.add("notf_side");

    // Adds the class corresponding to the notification type
    if (type === "success") {
        notf_side.classList.add("back-green");
    } else if (type === "error") {
        notf_side.classList.add("back-red");
    } else if (type === "info") {
        notf_side.classList.add("back-yellow");
    }

    // Creating the notification content
    var notf_content = document.createElement("div");
    notf_content.classList.add("notf_content");

    // Creating the notification image
    var notf_img = document.createElement("img");
    notf_img.classList.add("notf_img");

    // Defining the image according to the type
    if (type === "success") {
        notf_img.src = "image/Success.svg";
    } else if (type === "error") {
        notf_img.src = "image/error.svg";
    } else if (type === "info") {
        notf_img.src = "image/info.svg";
    }

    // Create the message area
    var messageDiv = document.createElement("div");
    messageDiv.classList.add("message");

    // Create the title and the message
    var notf_title = document.createElement("p");
    notf_title.classList.add("font", "notf_title");
    notf_title.textContent = type === "success" ? "Ação bem-sucedida" : (type === "error" ? "Erro" : "Aviso");

    var notf_message = document.createElement("p");
    notf_message.classList.add("font", "notf_message");
    notf_message.textContent = message;

    // Adds the title and the message to the content
    messageDiv.appendChild(notf_title);
    messageDiv.appendChild(notf_message);

    // Adds the image and the message to the notification's content
    notf_content.appendChild(notf_img);
    notf_content.appendChild(messageDiv);

    // Gets the notification ready
    notification.appendChild(notf_side);
    notification.appendChild(notf_content);

    // Adds the notification to the document's body
    document.body.appendChild(notification);

    // Controling the vertical position to stack the notifications
    notification.style.bottom = (20 + notificationCount * 140) + "px"; // Each new notification will appear a bit above the previous one
    notificationCount++; // Increments the counter for the next notification

    // Delays the display and intro animation
    setTimeout(() => {
        notification.classList.add("show"); // Displays the notification with animation
    }, 100);

    // Removes the notification after some time
    setTimeout(() => {
        notification.classList.add("hide");
    }, 3000);

    // Removes the element after the removal and adjusts the remaining notifications' position
    setTimeout(() => {
        notification.remove(); 
        notificationCount--; 
        repositionNotifications(); 
    }, 3500);
}

// Function to reposition notifications after one is removed
function repositionNotifications() {
    // Getting all the active notifications
    const notifications = document.querySelectorAll('.notification');

    // Repositioning the remaining notifications
    notifications.forEach((notif, index) => {
        index - 1
        if (index != 1) {
            notif.style.bottom = (20 + index * 140) + "px"; // Adjusting the position to ensure correct stacking
        } 
        else {
            notif.style.bottom = (20) + "px"; // Adjusting the position to ensure correct stacking
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    fetchIntercept();
});