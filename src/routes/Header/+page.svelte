<script lang="ts">
    import { onMount } from 'svelte';

    let isSideNavOpen = false;
    let isClosing = false;
    let fullName = ""; // Variable to store the logged-in user's name
    let studentId = "";

    // Function to toggle the side navigation
    function toggleSideNav() {
        if (isSideNavOpen) {
            isClosing = true;
            setTimeout(() => {
                isSideNavOpen = false;
                isClosing = false;
            }, 300); // Match the duration of the slide animation
        } else {
            isSideNavOpen = true;
        }
    }

    // Fetch the logged-in user's name
    async function fetchParticipantDetails() {
        const student_Id = localStorage.getItem('student_Id');
        if (!student_Id) {
            console.log("No student_Id found in localStorage.");
            return;
        }

        try {
            const response = await fetch('http://localhost:5000/api/participant/details', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ student_Id }),
            });

            if (!response.ok) {
                const error = await response.json();
                console.error("Error fetching participant details:", error.message || error);
                return;
            }

            const data = await response.json();

            // Ensure reactivity
            fullName = `${data.firstName} ${data.lastName}`;
            console.log("Fetched fullName:", fullName); // Debugging log
        } catch (error) {
            console.error("Error fetching participant details:", error);
        }
    }

    // Logout function
    function logout() {
        localStorage.removeItem('student_Id'); // Clear student ID
        sessionStorage.clear(); // Clear session storage
        window.location.href = '/'; // Redirect to login page
    }

    async function generateQRCode() {
    try {
        const response = await fetch(`/api/participant/${studentId}`);
        if (!response.ok) {
            throw new Error('Failed to generate QR code');
        }
        
        const data = await response.json();
        
        if (data && data.qr_code) {
            const qrCodeImg = document.createElement('img');
            qrCodeImg.src = `data:image/png;base64,${data.qr_code}`;
            
            const qrCodeContainer = document.getElementById('qrCodeContainer');
            if (qrCodeContainer) {
                qrCodeContainer.appendChild(qrCodeImg);
            } else {
                console.error("QR Code container not found.");
            }
        } else {
            alert("QR code not found.");
        }
        
    } catch (error: unknown) {
        if (error instanceof Error) {
            console.error(error.message);
            alert(error.message);
        } else {
            console.error("An unexpected error occurred.");
            alert("An unexpected error occurred.");
        }
    }
}



    // Mount component and fetch user details
    onMount(() => {
        fetchParticipantDetails();
    });
</script>

<style>
    .header-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        z-index: 1000; /* Adjust to ensure it appears above other elements */
    }

    .right-logos {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .easynergy-logo {
        width: 100px;
        cursor: pointer;
    }
</style>

<!-- Header Container -->
<div class="header-container flex justify-between items-center p-4 bg-white shadow">
    <!-- EASYnergy Logo trigger -->
    <button
        id="easynergy-logo"
        aria-label="Toggle Side Navigation"
        on:click={toggleSideNav}
        on:keydown={(event) => event.key === 'Enter' && toggleSideNav()}
        style="background: none; border: none; padding: 0; cursor: pointer;">
        <img src="/EASYnergy.png" alt="EASYnergy Logo" class="easynergy-logo"/>
    </button>

    <!-- Logged-in User Info -->
    <div class="user-info text-black text-lg flex items-center">
        {#if fullName}
            <span>Welcome, {fullName}!</span>
            <button 
                on:click={generateQRCode} 
                class="ml-4 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                Generate QR Code
            </button>
        {:else}
            <span>Loading participant details...</span>
        {/if}
    </div>

    <div id='qrCodeContainer'></div>

    <!-- Right Side Logos -->
    <div class="right-logos">
        <img src="/ccs_logo.png" alt="CCS Logo" class="w-20 h-auto"/>
        <img src="/gc_new_logo_2018.png" alt="GC Logo" class="w-20 h-auto"/>
    </div>
</div>