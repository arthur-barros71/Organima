var dragElement = document.getElementById("drag");       // Draggable area (moves the container)
var img = document.getElementById("dragImg");            // Resizable image
var imgWrapper = document.getElementById("dragImg2");    // Handles' area

if(imgWrapper) {
    var resizeHandles = imgWrapper.querySelectorAll(".resize-handle");
}

// Gets the container `.image`
if(dragElement) {
    var container = dragElement.parentElement;
}

//-------------------------------------------Movement function: moves dragElement-------------------------------------------

function DragElement(elem) {
    var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

    if (elem) {
        elem.onmousedown = dragMouseDown;
    }    

    function dragMouseDown(e) {
        // If the click is on a handle, don't start the movement
        if (e.target.classList.contains("resize-handle")) return;
        e.preventDefault();
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e.preventDefault();
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;

        var newTop = elem.offsetTop - pos2;
        var newLeft = elem.offsetLeft - pos1;

        // Limits the moviment inside the container (.image)
        var maxTop = container.clientHeight - elem.offsetHeight;
        var maxLeft = container.clientWidth - elem.offsetWidth;

        if (newTop < 0) newTop = 0;
        if (newLeft < 0) newLeft = 0;
        if (newTop > maxTop) newTop = maxTop;
        if (newLeft > maxLeft) newLeft = maxLeft;

        elem.style.top = newTop + "px";
        elem.style.left = newLeft + "px";

        // Updates also img and imgWrapper so that they move togheter
        img.style.top = newTop + "px";
        img.style.left = newLeft + "px";
        imgWrapper.style.top = newTop + "px";
        imgWrapper.style.left = newLeft + "px";
    }

    function closeDragElement() {
        document.onmouseup = null;
        document.onmousemove = null;
    }
}

//-------------------------------------------Resizing function-------------------------------------------
function ResizeElement(img, wrapper) {

    var currentHandle; // handle that started the resizing
    var startWidth, startHeight, startX, startY;

    // For each handle, assign the mousedown function
    if(resizeHandles) {
        resizeHandles.forEach(function(handle) {
            handle.onmousedown = function(e) {
                e.preventDefault();

                e.stopPropagation(); // prevent from starting the movement
                currentHandle = handle; // stores which handle is being used

                // Store the initial dimensions of the image
                startWidth = img.offsetWidth;
                startHeight = img.offsetHeight;

                // Register the mouse's initial position
                startX = e.clientX;
                startY = e.clientY;

                document.onmousemove = elementResize;
                document.onmouseup = closeResizeElement;
            };
        });
    }    

    function elementResize(e) {
        e.preventDefault();
        var dx = e.clientX - startX;
        var dy = e.clientY - startY;
        var newWidth, newHeight;

        // Each handle adjust the dimensions according to it's position, without changing the image's location
        if (currentHandle.classList.contains("bottom-right")) {
            newWidth = startWidth + dx;
            newHeight = startHeight + dy;
        } else if (currentHandle.classList.contains("bottom-left")) {
            newWidth = startWidth - dx;
            newHeight = startHeight + dy;
        } else if (currentHandle.classList.contains("top-right")) {
            newWidth = startWidth + dx;
            newHeight = startHeight - dy;
        } else if (currentHandle.classList.contains("top-left")) {
            newWidth = startWidth - dx;
            newHeight = startHeight - dy;
        }

        // Maximum size
        var minSize = 50;
        if (newWidth < minSize) newWidth = minSize;
        if (newHeight < minSize) newHeight = minSize;

        // Maximum limit according the container and the image's fixed position
        var maxWidth = container.clientWidth - img.offsetLeft;
        var maxHeight = container.clientHeight - img.offsetTop;
        if (newWidth > maxWidth) newWidth = maxWidth;
        if (newHeight > maxHeight) newHeight = maxHeight;

        // Updates only the dimensions
        img.style.width = newWidth + "px";
        img.style.height = newHeight + "px";
        wrapper.style.width = newWidth + "px";
        wrapper.style.height = newHeight + "px";
    }

    function closeResizeElement() {
        document.onmousemove = null;
        document.onmouseup = null;
    }
}

// Initialize the functions
DragElement(dragElement);
ResizeElement(img, imgWrapper);