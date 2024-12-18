<script lang="ts">
    import { onMount } from "svelte";
    import jsQR from "jsqr";
  
    let videoElement: HTMLVideoElement | null = null;
    let canvasElement: HTMLCanvasElement | null = null;
    let canvasContext: CanvasRenderingContext2D | null = null;
    let showScanner: boolean = false;
    let message: string = "";
    let isScanning: boolean = false;
    let isScanningStarted: boolean = false; // To trigger shrinking animation
    let cancelScanning: boolean = false; // To track if scanning was canceled
    let cameraActive: boolean = false; // To track if the camera is active
  
    const eventId: string = "12345"; // Replace with dynamic event ID if needed
  
    // Function to start the camera and scanning process
    const startScanner = async () => {
      isScanningStarted = true; // Trigger shrinking animation
      setTimeout(() => {
        showScanner = true;
        isScanning = true;
        cameraActive = true; // Camera is active
      }, 1000); // Delay to allow the shrinking effect to complete before showing camera
  
      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: "environment" },
        });
        if (videoElement) {
          videoElement.srcObject = stream;
          videoElement.setAttribute("playsinline", "true");
          videoElement.play();
          scanQRCode();
        }
      } catch (error) {
        message = "Unable to access the camera. Please grant camera permissions.";
        console.error(error);
      }
    };
  
    // Function to continuously scan for QR codes
    const scanQRCode = async () => {
      if (videoElement && videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
        if (canvasElement && canvasContext) {
          canvasElement.width = videoElement.videoWidth;
          canvasElement.height = videoElement.videoHeight;
          canvasContext.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
          const imageData = canvasContext.getImageData(0, 0, canvasElement.width, canvasElement.height);
          const qrCode = jsQR(imageData.data, imageData.width, imageData.height);
  
          if (qrCode) {
            handleQRCode(qrCode.data); // Handle QR code if found
          } else if (isScanning) {
            setTimeout(scanQRCode, 100); // Continue scanning if no QR code is found
          }
        }
      } else if (isScanning) {
        requestAnimationFrame(scanQRCode);
      }
    };
  
    // Function to handle the scanned QR code
    const handleQRCode = async (data: string) => {
      isScanning = false;
  
      try {
        const response = await fetch("/api/attendance/check", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ eventId, qrCode: data }),
        });
  
        const result = await response.json();
  
        if (result.success) {
          message = "✅ Attendance recorded successfully!";
        } else {
          message = `❌ ${result.message}`;
        }
      } catch (error) {
        message = "❌ Error validating the QR code. Please try again.";
        console.error(error);
      }
    };
  
    // Function to cancel scanning process
    const cancelScan = () => {
      cancelScanning = true;
      cameraActive = false; // Camera is off
      showScanner = false;
      isScanningStarted = false; // Reset the shrinking effect
      if (videoElement && videoElement.srcObject) {
        const stream = videoElement.srcObject as MediaStream;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
      }
    };
  
    // Initialize canvas when component is mounted
    onMount(() => {
      canvasElement = document.createElement("canvas");
      canvasContext = canvasElement.getContext("2d");
    });
  </script>
  
  <main class="min-h-screen bg-[#400000] text-gray-800 flex flex-col items-center justify-center overflow-hidden">
    <!-- Logos and Title Section -->
    <div class="flex flex-col items-center">
        <img src="/EASYnergy.png" alt="EASYnergy Logo" class="h-20" />
      <div class="flex space-x-4 mb-">
        <img src="/ccs_logo.png" alt="CCS Logo" class="h-16" />
        <img src="/gc_new_logo_2018.png" alt="GC Logo" class="h-16" />
      </div>
  
      <h1 class="text-4xl text-orange-600 font-bold text-center mb-2">GORDON COLLEGE</h1>
      <h2 class="text-2xl text-orange-600 font-semibold text-center mb-6">COLLEGE OF COMPUTER STUDIES</h2>
  
      <!-- CCS SC Logo and Scan Button -->
      <div class="flex flex-col items-center">
        {#if !showScanner}
          <!-- Display CCS Logo -->
          <div class={`mb-4 ${isScanningStarted ? 'animate-shrink-logo' : ''}`}>
            <img src="/ccs_sc_logo_4.png" alt="CCS SC Logo" class="h-20 transition-all" />
          </div>
          <button
            on:click={startScanner}
            class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600"
          >
            Start Scanning
          </button>
        {/if}
        {#if cancelScanning && !cameraActive}
          <!-- Cancel Button and Text -->
          <p class="text-red-500 mb-4">Scanning Cancelled</p>
        {/if}
      </div>
    </div>
  
    <!-- Scanner Section -->
    {#if showScanner}
      <div class="flex flex-col items-center mt-6">
        <h2 class="text-xl text-white font-semibold mb-4">Scan the QR Code</h2>
        <video
          bind:this={videoElement}
          class="border-2 border-orange-300 rounded-lg"
          muted
        >
          <track kind="captions" label="English" src="#" default />
        </video>
        <p class="mt-4 text-lg text-gray-600">{message}</p>
        <button
          on:click={cancelScan}
          class="px-6 py-3 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 mt-4"
        >
          Cancel Scanning
        </button>
      </div>
    {/if}
  </main>
  
  <style>
    video {
      width: 225px;
      height: 225px;
    }
  
    /* Shrinking animation for the logo */
    @keyframes shrink-logo {
      0% {
        transform: scale(1);
      }
      100% {
        transform: scale(0.5);
      }
    }
  
    .animate-shrink-logo img {
      animation: shrink-logo 1s ease forwards;
    }
  </style>
  