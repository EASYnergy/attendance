<script lang="ts">
  import { goto } from '$app/navigation';

  let studentId = ''; // Student ID
  let password = '';
  let firstname = '';
  let lastname = '';
  let email = '';
  let department = '';
  let isSignup = false;
  let currentImageIndex = 0;

  // Utility function to handle API requests
  async function apiRequest(url: string, data: object) {
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      });

      const responseData = await response.json();

      if (!response.ok) {
        // Throw the error message from the API response
        throw new Error(responseData.error || 'An unknown error occurred');
      }

      return responseData; // Return data if successful
    } catch (error) {
      if (error instanceof Error) {
        console.error('API Error:', error.message);
        alert('An error occurred: ' + error.message);
      } else {
        console.error('Unknown Error:', error);
        alert('An unknown error occurred.');
      }
      throw error; // Re-throw the error to be handled by the caller
    }
  }

  async function handleLogin() {
    if (studentId.trim() && password.trim()) {
      try {
        const data = await apiRequest('http://localhost:5000/api/participant/login', {
          student_Id: studentId,
          password,
        });

        // Store student_Id in localStorage
        localStorage.setItem('student_Id', data.participant.student_Id);

        // Successful login
        alert(`Welcome, ${data.participant.firstName}${data.participant.lastName}!`);
        goto('/dashboard'); // Navigate to the dashboard
      } catch (error) {
        // Error already handled in apiRequest
      }
    } else {
      alert('Please fill in both Student ID and password.');
    }
  }

  async function handleSignup() {
    if (
      firstname.trim() &&
      lastname.trim() &&
      email.trim() &&
      department.trim() &&
      studentId.trim() &&
      password.trim()
    ) {
      try {
        const data = await apiRequest('http://localhost:5000/api/participant/signup', {
          student_Id: studentId,
          password,
          firstName: firstname,
          lastName: lastname,
          email,
          department,
        });
        alert('Signup successful!');
        isSignup = false;
      } catch (error) {
        if (error instanceof Error) {
          alert(`Signup failed: ${error.message}`);
        } else {
          alert('Signup failed: An unknown error occurred.');
        }
      }
    } else {
      alert('Please fill in all fields.');
    }
  }

  let images = [
    "/ccs_logo.png",
    "/gc_new_logo_2018.png",
    "/EASYnergy.png",
  ];

// Cycle through logos
function cycleLogo() {
    currentImageIndex = (currentImageIndex + 1) % images.length;
  }

  // Start logo cycling
  setInterval(cycleLogo, 5000);

</script>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap');

  h1, h2, form {
    font-family: 'Raleway', sans-serif;
  }

  .logo-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
    backdrop-filter: blur(5px);
  }

  .logo {
    max-height: 4rem;
    max-width: 200px;
    object-fit: contain;
    position: absolute;
    opacity: 0;
    transition: opacity 2s ease-in-out;
  }

  .logo.active {
    opacity: 3;
  }

  select {
    background-color: transparent;
    color: white;
  }

  select option {
    background-color: #663333;
    color: white;
  }

  select option:hover {
    background-color: orange;
  }

  select:focus {
    outline: none;
  }
  
</style>

<div class="content-wrapper">
  <div class="logo-container">
    {#each images as image, index}
    <img
      src={image}
      alt={`Logo ${index}`}
      class="logo {index === currentImageIndex ? 'active' : ''}"
    />
  {/each} 
  </div>

<div class="fixed w-full h-full pb-0 grid grid-cols-3" style="background-color: #400000; top: 0; left: 0;">
    <!-- Left Column -->
    <div class="flex items-center justify-start bg-transparent opacity-70">
      <img
        src="/right_half_log.png"
        alt="Right Half Logo"
        class="h-auto w-full max-w-[64%] object-contain"/>
    </div>

  <!-- Center Column -->
  <div class="flex flex-col items-center justify-center bg-transparent text-white px-4 py-">
      <div class="mb-8 text-center">
        <h1 class="text-2xl md:text-3xl font-bold text-orange-400">GORDON COLLEGE</h1>
        <h2 class="text-xl md:text-2xl font-semibold text-orange-400 mt-2">
          COLLEGE OF COMPUTER STUDIES
        </h2>
      </div>



    <!-- Form -->
    <div class="w-96 p-6 pb-4 bg-white bg-opacity-20 backdrop-blur-md rounded-lg shadow-lg">
      {#if isSignup}
        <!-- Signup Form -->
        <h2 class="text-2xl font-bold text-white text-center mb-3">Signup</h2>
        <form on:submit|preventDefault={handleSignup}>
          <div class="mb-4">
            <label for="studentId" class="block text-sm font-medium text-white">Student ID</label>
            <input
              type="text"
              id="studentId"
              bind:value={studentId}
              placeholder="Enter your Student ID"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-white rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>  
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-white">Password</label>
            <input
              type="password"
              id="password"
              bind:value={password}
              placeholder="Enter your password"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-white rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>
          <div class="mb-4">
            <label for="firstname" class="block text-sm font-medium text-white">Firstname</label>
            <input
              type="text"
              id="firstname"
              bind:value={firstname}
              placeholder="Enter your firstname"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-white rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>
          <div class="mb-4">
            <label for="lastname" class="block text-sm font-medium text-white">Lastname</label>
            <input
              type="text"
              id="lastname"
              bind:value={lastname}
              placeholder="Enter your lastname"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-white rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-white">Student Email</label>
            <input
              type="email"
              id="email"
              bind:value={email}
              placeholder="Enter your email"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-white rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>

          <div class="mb-4">
            <label for="department" class="block text-sm font-medium text-white">Department</label>
            <select
              id="department"
              bind:value={department}
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-white rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none">
              <option value="" disabled selected>Select your department</option>
              <option value="College of Computer Studies">College of Computer Studies</option>
              <option value="College of Business and Accountancy">College of Business and Accountancy</option>
              <option value="College of Education, Arts and Science">College of Education, Arts and Science</option>
              <option value="College of Hospitality and Tourism Management">College of Hospitality and Tourism Management</option>
              <option value="College of Allied Health Studies">College of Allied Health Studies</option>
            </select>
          </div>
          
          <button
            type="submit"
            class="w-full py-2 px-4 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 focus:none focus:outline-none">
            Signup
          </button>
        </form>
        <p class="text-sm text-center text-white mt-4">
          Already have an account? <button type="button" class="font-semibold text-orange-400 hover:underline" on:click={() => (isSignup = false)}>Login here</button>
        </p>
      {:else}
      
        <!-- Login Form -->
        <h2 class="text-2xl font-bold text-white text-center mb-6">Login</h2>
        <form on:submit|preventDefault={handleLogin}>
          <div class="mb-4">
            <label for="studentId" class="block text-sm font-medium text-white">Student ID</label>
            <input
              type="text"
              id="studentId"
              bind:value={studentId}
              placeholder="Enter your Student ID"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-white rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"
            />
          </div>
          <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-white">Password</label>
            <input
              type="password"
              id="password"
              bind:value={password}
              placeholder="Enter your password"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-white rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"
            />
          </div>
          <button
            type="submit"
            class="w-full py-2 px-4 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 focus:none focus:outline-none">
            Login
          </button>
        </form>
        <p class="text-sm text-center text-white mt-4">
          Don't have an account? <button type="button" class="font-semibold text-orange-400 hover:underline" on:click={() => (isSignup = true)}>Signup here</button>
        </p>
      {/if}
    </div>
  </div>

  <!-- Right Column -->
  <div class="flex items-center justify-end bg-transparent opacity-70">
    <img src="/left_half_logo.png" alt="Left Half Logo" class="h-[60%] w-auto" />
  </div>
</div>
</div>
