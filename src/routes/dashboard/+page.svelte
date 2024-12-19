<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import Header from '../Header/+page.svelte'; // Import Header component
  
    interface User {
      email: string;
      role: string;
    }
  
    interface Event {
      event_name: string;
      event_description: string;
      location: string;
      event_date: string;
      user_id: number;
      user: User;
    }
  
    const defaultEvents: Event[] = [
      {
        event_name: "Web Development Workshop",
        event_description: "Learn modern web development techniques and best practices",
        location: "Room 301, Tech Building",
        event_date: "2024-12-20",
        user_id: 1,
        user: {
          email: "professor.smith@university.edu",
          role: "Senior Faculty"
        }
      },
      {
        event_name: "Data Science Seminar",
        event_description: "Introduction to data analysis and machine learning",
        location: "Virtual Meeting Room",
        event_date: "2024-12-22",
        user_id: 2,
        user: {
          email: "dr.jones@university.edu",
          role: "Department Head"
        }
      },
      {
        event_name: "Student Orientation",
        event_description: "Welcome session for new students",
        location: "Main Auditorium",
        event_date: "2024-12-25",
        user_id: 3,
        user: {
          email: "prof.wilson@university.edu",
          role: "Faculty Advisor"
        }
      }
    ];
  
    let searchQuery = '';
    let filteredEvents: Event[] = defaultEvents;
  
    // Search function
    $: {
      if (searchQuery) {
        filteredEvents = defaultEvents.filter(event =>
          event.event_name.toLowerCase().includes(searchQuery.toLowerCase()) ||
          event.event_description.toLowerCase().includes(searchQuery.toLowerCase()) ||
          event.location.toLowerCase().includes(searchQuery.toLowerCase())
        );
      } else {
        filteredEvents = defaultEvents;
      }
    }
  
    // Event details navigation
    const navigateToEventDetails = (event_id: number): void => {
    goto(`/eventdetails/${event_id}`); // Use goto to navigate to the event page
  };
  </script>
  
  <Header />
  
  <div class="container mt-20 mx-auto px-4 py-8 max-w-7xl">
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
              <h3 class="text-xl font-semibold text-gray-800 mb-2">{event.event_name}</h3>
              <p class="text-gray-600 mb-4">{event.event_description}</p>
  
              <!-- Event Details -->
              <div class="space-y-2 border-t border-orange-500 pt-4">
                <div class="flex items-start">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <p class="text-gray-700">{event.location}</p>
                </div>
  
                <div class="flex items-start">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-gray-700">{new Date(event.event_date).toLocaleDateString()}</p>
                </div>
              </div>
  
              <!-- User Info -->
              <div class="border-t border-orange-500 mt-4 pt-4 space-y-2">
                <div class="flex items-start">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <div>
                    <p class="text-gray-700">{event.user.email}</p>
                    <p class="text-sm text-gray-500">{event.user.role}</p>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Enter Button -->
            <!-- Enter Button -->
<div class="flex justify-end mt-6 pt-4 border-t border-orange-500">
    <button
      on:click={() => navigateToEventDetails(event.user_id)}
      class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2"
    >
      <span>Enter</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
      </svg>
    </button>
  </div>
  
          </div>
        </div>
      {/each}
    </div>
  </div>
  