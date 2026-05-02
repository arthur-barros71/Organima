//-----------------------------------------------------Profile options-----------------------------------------------------

document.addEventListener('DOMContentLoaded', function() {
    
    var modalProfile = document.getElementById('ModalProfile'); // Gets the profile options modal

    function showProfileModal() {
        document.getElementById('profileSeta').src = "image/Seta.svg";
        modalProfile.classList.add("ModalProfileActive");
    }

    // Closes the modal if the user clicks outside it
    function closeProfileModal(event) {
        if (!modalProfile.contains(event.target) && !event.target.closest('.profile')) {
            document.getElementById('profileSeta').src = "image/SetaRight.svg";

            // Removes the class that activates the modal
            modalProfile.classList.remove("ModalProfileActive");

        }
    }

    // Adds the click event that opens the modal
    var profileButton = document.querySelector('.profile');
    if (profileButton) {
        profileButton.onclick = showProfileModal;
    }

    // Adds the click event that closes the modal
    document.addEventListener('click', closeProfileModal);
});

// Function that closes the profileModal without the user's click
function closeProfileModal1() {
    document.getElementById('profileSeta').src = "image/Seta.svg";
    document.getElementById('ModalProfile').classList.remove("ModalProfileActive");
}