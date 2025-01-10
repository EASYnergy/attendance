<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import type { Page } from '@sveltejs/kit';
  import Header from '../Header/+page.svelte';
  import jsQR from 'jsqr'; // Import jsQR library

  interface Event {
    event_name: string;
    event_description: string;
    location: string;
    event_date: string;
    start_time: string;
    end_time: string;
  }

  interface Participant {
  student_id: string;
  fullName: string;
  year_and_block: string;  
  department: string;
}



  let event: Event | null = null;
  let participant: Participant | null = null;
  let eventId: number | null = null;
  let loading = true;
  let error: string | null = null;

  let cameraStream: MediaStream | null = null;
  let timeoutId: number | null = null;
  let checkInTime: Date | null = null;
  let status: string = "Not Present"; // Default status


  const fetchParticipantData = async (eventId: number): Promise<void> => {
  try {
    console.log('Fetching data for eventId:', eventId);
    const response = await fetch(`http://localhost:5000/api/event_registration/${eventId}`);
    if (!response.ok) throw new Error('Failed to fetch participant details');

    const data = await response.json();
    console.log('Fetched participant data:', data);

    if (data.registrations && data.registrations.length > 0) {
      const registration = data.registrations[0]; // Assuming you want the first participant's details
      participant = {
        student_id: registration.student_id || '',
        fullName: registration.fullname || '',
        year_and_block: registration.year_and_block || '',
        department: registration.department || '',
      };
    } else {
      console.warn('No participant registrations found.');
      participant = {
        student_id: '',
        fullName: '',
        year_and_block: '',
        department: '',
      };
    }
  } catch (err) {
    console.error('Error fetching participant data:', err);
    participant = {
      student_id: '',
      fullName: '',
      year_and_block: '',
      department: '',
    };
  }
};



  const fetchEventData = async () => {
    try {
      const eventRes = await fetch(`http://localhost:5000/events/${eventId}`);
      if (!eventRes.ok) throw new Error('Failed to fetch event details');
      event = await eventRes.json();
      loading = false;
    } catch (err: any) {
      error = err.message;
      loading = false;
    }
  };

  const startCamera = async () => {
    try {
      cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
      const videoElement = document.getElementById('camera-preview') as HTMLVideoElement;
      const canvasElement = document.getElementById('camera-canvas') as HTMLCanvasElement;
      const canvasContext = canvasElement?.getContext('2d');

      if (videoElement) {
        videoElement.srcObject = cameraStream;
        videoElement.play();

        const scanQRCode = () => {
          if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;

            canvasContext?.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            const imageData = canvasContext?.getImageData(0, 0, canvasElement.width, canvasElement.height);
            if (imageData) {
              const qrCode = jsQR(imageData.data, imageData.width, imageData.height);
              if (qrCode) {
                console.log('QR Code detected:', qrCode.data);
                stopCamera();

                // Parse QR Code Data
                const [event_id, event_name, event_location] = qrCode.data.split(',');
                eventId = parseInt(event_id); // Assuming event_id is the first data in QR
                fetchEventData();
                fetchParticipantData(eventId);

                // Set check-in time if first scan
                if (!checkInTime) {
                  checkInTime = new Date();
                  status = 'Present'; // Mark as present on first scan
                }
              }
            }
          }

          if (cameraStream) {
            requestAnimationFrame(scanQRCode);
          }
        };

        scanQRCode();
      }

      timeoutId = window.setTimeout(stopCamera, 15000);
    } catch (error) {
      console.error('Error accessing the camera:', error);
    }
  };

  const stopCamera = () => {
    if (cameraStream) {
      const tracks = cameraStream.getTracks();
      tracks.forEach((track) => track.stop());
      cameraStream = null;
    }

    if (timeoutId) {
      clearTimeout(timeoutId);
    }
  };

  const navigateBackToDashboard = () => {
    goto('/dashboard');
  };

  const recordAttendance = async () => {
    try {
      const attendanceData = {
        event_id: eventId,
        student_id: participant?.student_id,
        fullname: participant?.fullName,
        year_and_block: participant?.year_and_block,
        department: participant?.department,
        check_in: checkInTime,
        status: 'Present',
      };

      const response = await fetch('http://localhost:5000/api/attendance', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(attendanceData),
      });

      if (!response.ok) throw new Error('Failed to record attendance');
      console.log('Attendance recorded:', attendanceData);
    } catch (err: any) {
      console.error(err.message);
    }
  };

  $: page.subscribe((p) => {
    const id = p.url.searchParams.get('id');
    eventId = id ? parseInt(id, 10) : null;
    console.log('Event ID:', eventId); // Debugging
    if (eventId) {
      fetchEventData();
    } else {
      error = 'Invalid or missing event ID';
      loading = false;
    }
  });
</script>

<Header />

{#if loading}
  <div class="text-center">Loading...</div>
{:else if error}
  <div class="text-red-500 text-center">Error: {error}</div>
{:else if event}
  <div class="container mx-auto mt-20 px-4 py-8 max-w-7xl flex">
    <!-- Left Div: Event Details -->
    <div class="flex-1 bg-white p-6 rounded-lg shadow-lg h-[400px]">
      <h1 class="text-3xl font-semibold text-gray-800 mb-4">{event.event_name}</h1>
      <p class="text-gray-600 mb-4">{event.event_description}</p>
      <div class="space-y-2">
        <p class="text-gray-700">{event.location}</p>
        <p class="text-gray-700">{new Date(event.event_date).toLocaleDateString()} | {event.start_time} - {event.end_time}</p>
      </div>
    </div>

    <!-- Right Div: QR Code Scanner -->
    <div class="flex-1 bg-white p-6 rounded-lg flex shadow-md flex-col justify-between h-[400px]">
      <video id="camera-preview" class="w-full h-[300px] bg-black rounded-lg" autoplay muted></video>
      <canvas id="camera-canvas" class="hidden"></canvas>
      <div class="mt-2 flex gap-4">
        <button
          on:click={startCamera}
          class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50">
          Start QR Code Scan
        </button>
      </div>
    </div>

    <!-- QR Code Scan Result Modal -->
    {#if participant}
    <div class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Attendance Details</h2>
        <p class="text-gray-700">Event ID: {eventId}</p>
        <p class="text-gray-700">Event Name: {event.event_name}</p>
        <p class="text-gray-700">Location: {event.location}</p>
        <p class="text-gray-700">Name: {participant.fullName}</p>
        <p class="text-gray-700">Student ID: {participant.student_id}</p>
        <p class="text-gray-700">Year and Block: {participant.year_and_block}</p>
        <p class="text-gray-700">Department: {participant.department}</p>
        <div class="mt-4 flex justify-center">
          <button
            on:click={() => { recordAttendance(); navigateBackToDashboard(); }}
            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            Okay
          </button>
        </div>
      </div>
    </div>
    {/if}
  </div>
{/if}
