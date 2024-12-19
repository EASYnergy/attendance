<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import Header from '../Header/+page.svelte'; // Import Header component

  // Interface for the participant
  interface Participant {
    firstname: string;
    lastname: string;
    student_id: string;
    registration_date: string;
  }

  // Sample Event Data (This would normally be passed dynamically via URL or API)
  let event = {
    event_name: "Web Development Workshop",
    event_description: "Learn modern web development techniques and best practices.",
    location: "Room 301, Tech Building",
    event_date: "2024-12-20",
    start_time: "09:00 AM",
    end_time: "12:00 PM",
    user: {
      email: "professor.smith@university.edu",
      role: "Senior Faculty"
    }
  };

  // Declare the participant variable with the correct type
  let participant: Participant | null = null;

  // QR Code Scanning Logic
  const scanQRCode = () => {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
      navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then((stream) => {
          // Access the camera stream and implement QR code scanning here
          const video = document.createElement('video');
          video.srcObject = stream;
          video.play();

          // QR Scanner logic (you can use libraries like `jsQR` or `zxing-js` for QR scanning)
          // Example: We simulate a successful scan here
          setTimeout(() => {
            // Simulate QR code scanning success (in real life, this would be decoded from the video stream)
            participant = {
              firstname: "John",
              lastname: "Doe",
              student_id: "123456",
              registration_date: new Date().toLocaleString()
            };
            stream.getTracks().forEach(track => track.stop()); // Stop the video stream
          }, 2000); // Simulating a delay for scanning
        })
        .catch((error) => {
          console.error("Error accessing camera:", error);
        });
    } else {
      alert("Camera access not supported in this browser.");
    }
  };

  // Navigate back to the Dashboard
  const navigateBackToDashboard = () => {
    goto('/dashboard');
  };
</script>

<Header />

<div class="container mx-auto mt-20 px-4 py-8 max-w-7xl flex">
  <!-- Left Div: Event Details -->
  <div class="flex-1 bg-white p-6 rounded-lg shadow-lg h-[400px]">
    <h1 class="text-3xl font-semibold text-gray-800 mb-4">{event.event_name}</h1>
    <p class="text-gray-600 mb-4">{event.event_description}</p>
    <div class="space-y-2">
      <div class="flex items-center text-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <p>{event.location}</p>
      </div>
      <div class="flex items-center text-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p>{new Date(event.event_date).toLocaleDateString()} | {event.start_time} - {event.end_time}</p>
      </div>
      <div class="flex items-center text-gray-700">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.485 0 4.5-1.57 4.5-3.5S14.485 5 12 5 7.5 6.57 7.5 8.5 9.515 12 12 12z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12l4 4m-4 0l-4-4" />
        </svg>
        <p>Created by: {event.user.email} ({event.user.role})</p>
      </div>
    </div>
  </div>

  <!-- Right Div: QR Scan -->
  <div class="flex-1 bg-white p-6 rounded-lg flex shadow-md flex-col justify-between h-[400px]">
    <!-- QR Code Scan Button -->
    <div class="mt-auto flex justify-center">
      <button
        on:click={scanQRCode}
        class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50"
      >
        Scan QR Code
      </button>
    </div>

    <!-- Camera View (Square Container) -->
    <div class="mt-8 flex justify-center items-center">
      <div class="w-64 h-64 bg-gray-100 flex justify-center items-center rounded-lg shadow-md overflow-hidden">
        <!-- This is where the camera feed will appear -->
        <video id="videoElement" class="w-full h-full object-cover hidden" autoplay muted>
          <track kind="captions" label="English" srcLang="en" default />
        </video>
        
      </div>
    </div>
  </div>
</div>

<!-- QR Code Scan Result Modal -->
{#if participant}
  <div class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4">Registration Successful</h2>
      <p class="text-gray-700">Registered Successfully on <strong>{event.event_name}</strong> at <strong>{event.location}</strong></p>
      <p class="text-gray-700">Name: {participant.firstname} {participant.lastname}</p>
      <p class="text-gray-700">Student ID: {participant.student_id}</p>
      <p class="text-gray-700">Date of Registration: {participant.registration_date}</p>
      <div class="mt-4 flex justify-center">
        <button
          on:click={navigateBackToDashboard}
          class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
        >
          Okay
        </button>
      </div>
    </div>
  </div>
{/if}
