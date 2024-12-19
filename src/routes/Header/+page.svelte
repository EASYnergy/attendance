<script lang="ts">
    import { onMount } from 'svelte';

    let isSideNavOpen = false;
    let isClosing = false;

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

    // Close nav when clicking outside
    function handleOutsideClick(event: MouseEvent) {
        const sideNav = document.getElementById('side-nav');
        const easynergyLogo = document.getElementById('easynergy-logo');
        
        if (isSideNavOpen && 
            sideNav && 
            easynergyLogo && 
            !sideNav.contains(event.target as Node) && 
            !easynergyLogo.contains(event.target as Node)
        ) {
            toggleSideNav();
        }
    }

    onMount(() => {
        document.addEventListener('click', handleOutsideClick);
        
        return () => {
            document.removeEventListener('click', handleOutsideClick);
        };
    });

    // Define the logout function
function logout() {
    // Clear session or token
    localStorage.removeItem('authToken'); // Example: remove auth token
    sessionStorage.clear(); // Example: clear session storage

    // Redirect to login or logout endpoint
    window.location.href = '/'; // Replace with your backend logout route
}
</script>

<style>
    .header-container {
        position: fixed;
        top: 0  ;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        z-index: 900; /* Reduced z-index to be behind the side nav */
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
<div class="header-container">
    <!-- EASYnergy Logo trigger -->
    <button
        id="easynergy-logo"
        aria-label="Toggle Side Navigation"
        on:click={toggleSideNav}
        on:keydown={(event) => event.key === 'Enter' && toggleSideNav()}
        style="background: none; border: none; padding: 0; cursor: pointer;"
    >
        <img src="/EASYnergy.png" alt="EASYnergy Logo" class="easynergy-logo"/>
    </button>

    <!-- Right Side Logos -->
    <div class="right-logos">
        <img src="/ccs_logo.png" alt="CCS Logo" class="w-20 h-auto"/>
        <img src="/gc_new_logo_2018.png" alt="GC Logo" class="w-20 h-auto"/>
    </div>
</div>

<!-- Side Navigation -->

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
