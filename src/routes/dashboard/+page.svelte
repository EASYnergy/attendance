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
  let createEventFormVisible = false; // Modal visibility
  let currentEvent: Event | null = null; // Track the current event for registration

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

  // Handle modal closing and simulate successful registration
  const closeEventRegistration = (): void => {
    if (currentEvent) {
      currentEvent.isRegistered = true;
      successMessage = `You have successfully registered for ${currentEvent.event_name}!`;
      setTimeout(() => (successMessage = ''), 3000); // Clear success message after 3 seconds
    }
    createEventFormVisible = false;
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
          <div class="flex justify-between mt-6 pt-4 border-t border-orange-500">
            <button
              on:click={() => openEventRegistration(event)}
              class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition-colors duration-200"
            >
              Register
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
      <input type="text" placeholder="Student ID" class="w-full mb-4 px-4 py-2 rounded-lg" />
      <button type="submit" class="px-4 py-2 text-white bg-orange-500 rounded-lg">
        Register
      </button>
    </form>
  </div>
</div>
{/if}
