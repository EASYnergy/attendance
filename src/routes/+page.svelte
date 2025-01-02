<script lang="ts">
  import { goto } from '$app/navigation';

  let studentId = ''; // Changed from email to Student ID
  let password = '';
  let firstname = '';
  let lastname = '';
  let email = '';
  let department = '';
  let isSignup = false;

  // Utility function to handle API requests
  async function apiRequest(url: string, data: object) {
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText);
      }

      return await response.json();
    } catch (error) {
      console.error('API Error:', error);
      alert('An error occurred. Please try again.');
      throw error;
    }
  }

  // Function to handle login
  async function handleLogin() {
    if (studentId.trim() && password.trim()) {
      try {
        const data = await apiRequest('http://localhost/attendance/login.php', { student_id: studentId, password });
        alert('Login successful!');
        goto('/dashboard'); // Navigate to dashboard
      } catch {
        alert('Login failed. Please check your credentials.');
      }
    } else {
      alert('Please fill in both Student ID and password.');
    }
  }

  // Function to handle signup
  async function handleSignup() {
    if (firstname.trim() && lastname.trim() && email.trim() && department.trim() && studentId.trim() && password.trim()) {
      try {
        const data = await apiRequest('http://localhost/attendance/signup.php', {
          firstname,
          lastname,
          email,
          department,
          student_id: studentId,
          password,
        });
        alert('Signup successful!');
        isSignup = false; // Switch back to login form
      } catch {
        alert('Signup failed. Please try again.');
      }
    } else {
      alert('Please fill in all fields.');
    }
  }
</script>

<div class="fixed w-full h-full grid grid-cols-3" style="background-color: #400000;">
  <!-- Left Column -->
  <div class="flex items-center justify-start bg-transparent opacity-70">
    <img src="/right_half_log.png" alt="Right Half Logo" class="h-[60%] w-auto" />
  </div>

  <!-- Center Column -->
  <div class="flex flex-col items-center justify-center bg-transparent text-white">
    <!-- Title -->
    <div class="mb-8 text-center">
      <h1 class="text-3xl font-bold text-orange-400">GORDON COLLEGE</h1>
      <h2 class="text-2xl font-semibold text-orange-400 mt-2">COLLEGE OF COMPUTER STUDIES</h2>
    </div>

    <!-- Form -->
    <div class="w-96 p-6 bg-white bg-opacity-20 backdrop-blur-md rounded-lg shadow-lg">
      {#if isSignup}
        <!-- Signup Form -->
        <h2 class="text-2xl font-bold text-white text-center mb-6">Signup</h2>
        <form on:submit|preventDefault={handleSignup}>
          <div class="mb-4">
            <label for="studentId" class="block text-sm font-medium text-white">Student ID</label>
            <input
              type="text"
              id="studentId"
              bind:value={studentId}
              placeholder="Enter your Student ID"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-black rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-white">Password</label>
            <input
              type="password"
              id="password"
              bind:value={password}
              placeholder="Enter your password"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-black rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>
          <div class="mb-4">
            <label for="firstname" class="block text-sm font-medium text-white">Firstname</label>
            <input
              type="text"
              id="firstname"
              bind:value={firstname}
              placeholder="Enter your firstname"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-black rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>
          <div class="mb-4">
            <label for="lastname" class="block text-sm font-medium text-white">Lastname</label>
            <input
              type="text"
              id="lastname"
              bind:value={lastname}
              placeholder="Enter your lastname"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-black rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-white">Student Email</label>
            <input
              type="email"
              id="email"
              bind:value={email}
              placeholder="Enter your email"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-black rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
          </div>
          <div class="mb-4">
            <label for="department" class="block text-sm font-medium text-white">Department</label>
            <input
              type="text"
              id="department"
              bind:value={department}
              placeholder="Enter your department"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-black rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"/>
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
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-black rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"
            />
          </div>
          <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-white">Password</label>
            <input
              type="password"
              id="password"
              bind:value={password}
              placeholder="Enter your password"
              class="mt-1 block w-full px-4 py-2 bg-transparent border-2 text-black rounded-lg focus:ring-2 focus:ring-orange-400 focus:outline-none"
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
