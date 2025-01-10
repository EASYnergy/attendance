<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import Header from '../Header/+page.svelte'; // Import Header component

  interface User {
    email: string;
    role: string;
  }

  interface Event {
    event_id: number;
    event_name: string;
    event_description: string;
    location: string;
    event_date: string;
    isRegistered?: boolean; // Track registration status per event
    speaker: string;
  }

  let events: Event[] = [];
  let searchQuery = '';
  let filteredEvents: Event[] = [];
  let successMessage = ''; // Store success message
  let errorMessage = ''; // Store error message
  let createEventFormVisible = false; // Modal visibility
  let currentEvent: Event | null = null; // Track the current event for registration

  // Registration form fields
  let student_id = '';
  let fullname = '';
  let year_and_block = '';
  let department = '';

  // Fetch events from the backend
  async function fetchEvents() {
    try {
      const response = await fetch('http://localhost:5000/api/events');
      if (response.ok) {
        events = await response.json();
        filteredEvents = [...events];
      } else {
        console.error('Failed to fetch events');
      }
    } catch (error) {
      console.error('Error fetching events:', error);
    }
  }

  // Delete an event
  async function deleteEvent(event_id: number) {
    try {
      const response = await fetch(`http://localhost:5000/api/events/${event_id}`, {
        method: 'DELETE',
      });
      if (response.ok) {
        successMessage = 'Event deleted successfully!';
        events = events.filter(event => event.event_id !== event_id);
        filteredEvents = [...events];
        setTimeout(() => (successMessage = ''), 3000);
      } else {
        console.error('Failed to delete the event');
      }
    } catch (error) {
      console.error('Error deleting event:', error);
    }
  }

  // Handle modal opening
  const openEventRegistration = (event: Event): void => {
    createEventFormVisible = true;
    currentEvent = event;
  };

  // Handle modal closing and registration
  const closeEventRegistration = async (): Promise<void> => {
    if (currentEvent) {
      try {
        const response = await fetch('http://localhost:5000/api/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            student_id,
            fullname,
            year_and_block,
            department,
            event_id: currentEvent.event_id, // Use event ID from the current event
          }),
          mode: 'cors'
        });

        const result = await response.json();

        if (response.ok) {
          currentEvent.isRegistered = true;
          successMessage = `You have successfully registered for ${currentEvent.event_name}!`;
          setTimeout(() => (successMessage = ''), 3000); // Clear success message after 3 seconds
        } else {
          errorMessage = result.error || 'An error occurred. Please try again.';
          setTimeout(() => (errorMessage = ''), 3000); // Clear error message after 3 seconds
        }
      } catch (error) {
        console.error('Error registering for event:', error);
        errorMessage = 'An unexpected error occurred. Please try again.';
        setTimeout(() => (errorMessage = ''), 3000);
      }
    }

    // Clear modal visibility and reset fields
    createEventFormVisible = false;
    student_id = '';
    fullname = '';
    year_and_block = '';
    department = '';
  };

  // Search function
  $: {
    if (searchQuery) {
      filteredEvents = events.filter(event =>
        event.event_name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        event.event_description.toLowerCase().includes(searchQuery.toLowerCase()) ||
        event.location.toLowerCase().includes(searchQuery.toLowerCase())
      );
    } else {
      filteredEvents = [...events];
    }
  }

  onMount(() => {
    fetchEvents();
  });

  // Navigate to event details
  function goToEventDetails(event: Event): void {
    goto(`/eventdetails?id=${event.event_id}`);
}

</script>


<Header />

<div class="container mt-20 mx-auto px-4 py-8 max-w-7xl">
  <!-- Success Prompt -->
  {#if successMessage}
    <div class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded-lg shadow-md">
      {successMessage}
    </div>
  {/if}

  <!-- Search Bar -->
  <div class="mb-6">
    <input
      type="text"
      bind:value={searchQuery}
      placeholder="Search events..."
      class="w-full md:w-80 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent shadow-sm"
    />
  </div>

  <!-- Events Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  {#each filteredEvents as event}
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
      <div class="p-6 flex flex-col h-full">
        <div class="flex-grow">
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Event: {event.event_name}</h3>
          <p class="text-gray-600 mb-4">Description: {event.event_description}</p>
          <p class="text-gray-700">Location: {event.location}</p>
          <p class="text-gray-700">Date: {new Date(event.event_date).toLocaleDateString()}</p>
          <p class="text-gray-600 mb-4">Speaker: {event.speaker}</p>
        </div>
        <div class="flex justify-between items-center mt-6 pt-4 border-t border-orange-500">
          <!-- Register Button -->
          <button
            on:click={() => openEventRegistration(event)}
            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
            Register
          </button>
          <!-- Circle with Arrow Button -->
          <button
              on:click={() => goToEventDetails(event)}
              class="flex items-center justify-center w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-full transition-colors duration-200 ml-2"
              aria-label="View event details">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
           <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
          </button>
        </div>
      </div>
    </div>
  {/each}
</div>
</div>

<!-- Modal Form -->
{#if createEventFormVisible}
<div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center">
  <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
    <h2 class="text-2xl font-bold mb-4">Register for {currentEvent?.event_name}</h2>
    <form on:submit|preventDefault={closeEventRegistration}>
      <input
        type="text"
        placeholder="Student ID"
        bind:value={student_id}
        class="w-full mb-4 px-4 py-2 rounded-lg border border-gray-300"/>
      <input
        type="text"
        placeholder="Fullname"
        bind:value={fullname}
        class="w-full mb-4 px-4 py-2 rounded-lg border border-gray-300"/>
      <input
        type="text"
        placeholder="Year and Block"
        bind:value={year_and_block}
        class="w-full mb-4 px-4 py-2 rounded-lg border border-gray-300"/>
      <input
        type="text"
        placeholder="Department"
        bind:value={department}
        class="w-full mb-4 px-4 py-2 rounded-lg border border-gray-300"/>
      <button
        type="submit"
        class="px-4 py-2 text-white bg-orange-500 rounded-lg hover:bg-orange-600">
        Register
      </button>
    </form>
  </div>
</div>
{/if}
