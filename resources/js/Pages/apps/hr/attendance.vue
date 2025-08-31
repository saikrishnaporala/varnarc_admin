<script lang="ts" setup>
  import { ref, onMounted, computed, reactive } from 'vue';
  import { Head } from '@inertiajs/vue3';
  import moment from 'moment';
  import Swal from 'sweetalert2';
  import '@fullcalendar/core';
  import simpleBar from 'simplebar-vue';
  import { CalendarIcon } from '@zhuowenli/vue-feather-icons';
  import flatPickr from 'vue-flatpickr-component';
  import 'flatpickr/dist/flatpickr.css';

  import dayGridPlugin from '@fullcalendar/daygrid';
  import timeGridPlugin from '@fullcalendar/timegrid';
  import interactionPlugin, { Draggable } from '@fullcalendar/interaction';
  import bootstrapPlugin from '@fullcalendar/bootstrap';
  import listPlugin from '@fullcalendar/list';
  import FullCalendar from '@fullcalendar/vue3';

  import { required, helpers } from '@vuelidate/validators';
  import useVuelidate from '@vuelidate/core';

  import Layout from '@/Layouts/main.vue';
  import PageHeader from '@/Components/page-header.vue';

  import { INITIAL_EVENTS, categories, attendanceRecords } from './utils';
  import { Event } from '@/interfaces/Event';
  import axios from 'axios';
  import { ActivityLog, Attendance, WorkHoursDayWise } from '@/interfaces/ActivityLog';
  import dayjs from 'dayjs';

  const employeeId = 1;
  const isPunchedIn = ref(false);
  const formattedCheckInTime = ref<string>('');
  const checkInTimeDisplay = ref('');
  const elapsedTime = ref<string>('');
  let timerInterval: NodeJS.Timer | null = null;

  const duration = ref<{ hours: number; minutes: number; seconds: number } | null>(null);
  const overtimeHours = ref(0);
  const hoursThisMonth = ref(90);
  const remainingHours = ref(160 - hoursThisMonth.value);
  const numMilliSecondsPerDay = ref(0);
  const numHoursPerDay = ref(0);
  const hoursLeftForToday = ref(0);
  const hoursLeftForWeek = ref(0);
  const hoursLeftForMonth = ref(0);

  const percentageForToday = ref<number>(0);
  const percentageForWeek = ref<number>(0);
  const percentageForMonth = ref<number>(0);

  const activityLogs = ref<ActivityLog[]>([]);
  const checkinList = ref<Attendance[]>([]);

  const attendanceEvents = ref<Event[]>([]);

  // const selectedAttendance = reactive<Attendance | null>(null);
  // const isModalOpen = ref(false);
  const isModalOpen = ref(true);
  const selectedAttendance = reactive({
    id: 0,
    date: '',
    checkIn: '',
    checkOut: ''
  });

  const updateAttendance = () => {
    // Save the updated check-in and check-out times
    isModalOpen.value = false;
  };

  const calendarOptions = ref({
    timeZone: 'local',
    droppable: true,
    navLinks: true,
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, bootstrapPlugin, listPlugin],
    themeSystem: 'bootstrap',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
    },
    // windowResize: () => getInitialView(),
    // initialView: getInitialView(),
    events: attendanceEvents.value,
    editable: true,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    dateClick: (info: any) => editAttendanceClicked(info),
    eventClick: (info: any) => editAttendanceClicked(info),
    // eventsSet: (events: any) => handleEvents(events),
  });

  onMounted(() => {
    checkTodayCheckInStatus();
    fetchWeeklyAttendance();
    fetchMonthlyAttendance();
    new Draggable(document.getElementById('external-events')!, {
      itemSelector: '.external-event',
      eventData: (eventEl: HTMLElement) => ({
        title: eventEl.innerText,
        start: new Date(),
        className: eventEl.getAttribute('data-class') || '',
      }),
    });
  });

  // Punch In/Out Toggle
  const togglePunch = async () => {
    isPunchedIn.value = !isPunchedIn.value;
    if(isPunchedIn.value) {
      console.log("Call Checkin");
      await checkIn();
    } else {
      console.log("Call Checkout");
      await checkOut();
    }
  };

  // Function to handle check-in
  const checkIn = async () => {
    // try {
    //   const response = await axios.post('/api/attendance/checkin', { employee_id: employeeId });
    //   console.log('Check-in successful:', response.data);
    //   checkInTimeDisplay.value = response.data.check_in;
    //   isPunchedIn.value = true;
    //   // Set check-in time and format it
    //   const checkInTime = new Date(response.data.created_at);
    //   formattedCheckInTime.value = checkInTime.toLocaleTimeString();
    //   checkInTimeDisplay.value = checkInTime.toString();
    //   // Calculate elapsed time and start updating it every second
    //   elapsedTime.value = formatElapsedTime(checkInTime);
    //   timerInterval = setInterval(() => {
    //     elapsedTime.value = formatElapsedTime(checkInTime);
    //   }, 1000);
    // } catch (error) {
    //   console.error('Check-in failed:', error);
    // }
  };

  // Function to handle check-out
  const checkOut = async () => {
    try {
      const response = await axios.post('/api/attendance/checkout', { employee_id: employeeId });
      console.log('Check-out successful:', response.data);
    } catch (error) {
      console.error('Check-out failed:', error);
    }
  };

  //Function to check today's Status
  const checkTodayCheckInStatus = async () => {
    try {
      // Make the API request
      const response = await axios.post('/api/attendance/checkin-status', {
        employee_id: employeeId,
      });
      checkinList.value = response.data.check_in_time;
      checkinList.value.sort((a, b) => a.id - b.id);
      checkinList.value.forEach((record) => {
        activityLogs.value.push({ time: convertTo12HourFormat(record.check_in), action: 'Check in' });
        if(record.check_out != "" || record.check_out != null){
          activityLogs.value.push({ time: convertTo12HourFormat(record.check_out), action: 'Check Out' });
        }
        if (record.check_in && record.check_out) {
          record.ms_worked = calculateMinutesWorked(record.check_in, record.check_out);
          numMilliSecondsPerDay.value = numMilliSecondsPerDay.value + record.ms_worked;
        }
      });
      numHoursPerDay.value = parseFloat((numMilliSecondsPerDay.value / (1000 * 60 * 60)).toFixed(2));
      const hours = numMilliSecondsPerDay.value / (1000 * 60 * 60);
      const minutes = (numMilliSecondsPerDay.value % (1000 * 60 * 60)) / (1000 * 60);
      const seconds = (numMilliSecondsPerDay.value % (1000 * 60)) / 1000;

      // Set the duration
      duration.value = { hours, minutes, seconds };
      duration.value.hours = Math.floor(hours);
      duration.value.minutes = Math.floor(minutes);
      duration.value.seconds = Math.floor(seconds);
      
      const today = new Date();
      const dayOfWeek = today.toLocaleString('en-US', { weekday: 'long' });
      const standardHours = getHoursByDay(dayOfWeek);
      if(hours == 0) {
        percentageForToday.value = 0;  
      } else {
        percentageForToday.value = calculateWorkPercentage(hours, standardHours);
      }
      console.log(percentageForToday.value);
      hoursLeftForToday.value = standardHours-(minutes/60);
      if(hours > standardHours) {
        overtimeHours.value = hours-standardHours;
      }
      if(response.data.check_in_time.length > 0) {
        var temp = response.data.check_in_time[0];
        if(temp.check_out != null){
          isPunchedIn.value = false;
        } else {
          isPunchedIn.value = true;
        }
      }
      // Check the response and set the message
      if (response.data.checked_in_today) {
        var temp = response.data.check_in_time[0];
        if(temp.check_out != null){
          isPunchedIn.value = false;
        } else {
          isPunchedIn.value = response.data.checked_in_today;
          // Set check-in time and format it
          const checkInTime = new Date(temp.created_at);
          formattedCheckInTime.value = checkInTime.toLocaleTimeString();
          checkInTimeDisplay.value = checkInTime.toString();
          // Calculate elapsed time and start updating it every second
          elapsedTime.value = formatElapsedTime(checkInTime);
          timerInterval = setInterval(() => {
            elapsedTime.value = formatElapsedTime(checkInTime);
          }, 1000);
        }
      } else {
        // checkedInTodayMessage.value = 'Employee has not checked in today.';
      }
    } catch (error) {
      console.error('Error checking today\'s check-in status:', error);
      // checkedInTodayMessage.value = 'Error fetching check-in status. Please try again later.';
    }
  };

  // Function to fetch weekly attendance
  const fetchWeeklyAttendance = async () => {
    try {
      const response = await axios.get(`/api/attendance/weekly/${employeeId}`);
      checkinList.value = response.data.data;
      checkinList.value.sort((a, b) => a.id - b.id);
      let ms = 0;
      checkinList.value.forEach((record) => {
        if (record.check_in && record.check_out) {
          record.ms_worked = calculateMinutesWorked(record.check_in, record.check_out);
          ms = ms + record.ms_worked;
        }
      });
      const hours = ms / (1000 * 60 * 60);
      const minutes = (ms % (1000 * 60 * 60)) / (1000 * 60);
      const seconds = (ms % (1000 * 60)) / 1000;
      const totalHoursPerWeek = getTotalHoursThisWeek();
      if(hours == 0) {
        percentageForWeek.value = 0;  
      } else {
        percentageForWeek.value = calculateWorkPercentage(hours, totalHoursPerWeek);
      }
      hoursLeftForWeek.value = totalHoursPerWeek-hours;
    } catch (error) {
      console.error('Failed to fetch weekly attendance:', error);
    }
  };

  // Function to fetch monthly attendance
  const fetchMonthlyAttendance = async () => {
    try {
      const response = await axios.get(`/api/attendance/monthly/${employeeId}`);
      checkinList.value = response.data.data;
      checkinList.value.sort((a, b) => a.id - b.id);
      let ms = 0;
      checkinList.value.forEach((record) => {
        if (record.check_in && record.check_out) {
          record.ms_worked = calculateMinutesWorked(record.check_in, record.check_out);
          ms = ms + record.ms_worked;
        }
      });
      const hours = ms / (1000 * 60 * 60);
      const minutes = (ms % (1000 * 60 * 60)) / (1000 * 60);
      const seconds = (ms % (1000 * 60)) / 1000;
      const totalHoursPerMonth = getTotalWorkingHoursForMonth();
      if(hours == 0) {
        percentageForMonth.value = 0;  
      } else {
        percentageForMonth.value = calculateWorkPercentage(hours, totalHoursPerMonth);
      }
      hoursLeftForMonth.value = totalHoursPerMonth-hours;
      attendanceEvents.value = convertAttendanceToEvents(checkinList.value);
      calendarOptions.value.events = attendanceEvents.value;
      console.log(attendanceEvents.value);
    } catch (error) {
      console.error('Failed to fetch monthly attendance:', error);
    }
  };

  const editAttendanceClicked = (info: any) => {
    const eventDate = dayjs(info.dateStr).format('YYYY-MM-DD');
    const attendance = attendanceEvents.value.find(att => att.id === eventDate);

    if (attendance && attendance.title === 'Present') {
      selectedAttendance.id = attendance.id;
      selectedAttendance.date = attendance.date;
      selectedAttendance.checkIn = attendance.checkIn;
      selectedAttendance.checkOut = attendance.checkOut;
      isModalOpen.value = true;
    }
  };

  // Function to convert from 24 hrs to 12hrs
  function convertTo12HourFormat(time24: string): string {
    const [hours, minutes] = time24.split(':').map(Number);
    const period = hours >= 12 ? 'PM' : 'AM';
    const hours12 = hours % 12 || 12;
    return `${hours12}:${String(minutes).padStart(2, '0')} ${period}`;
  }

  function calculateMinutesWorked(checkIn: string, checkOut: string): number {
    const checkInTime = new Date(`1970-01-01T${checkIn}Z`);
    const checkOutTime = new Date(`1970-01-01T${checkOut}Z`);

    // Calculate the difference in milliseconds
    const diffInMilliseconds = checkOutTime.getTime() - checkInTime.getTime();

    // Convert milliseconds to minutes
    const diffInMinutes = Math.floor(diffInMilliseconds / (1000 * 60));

    return diffInMilliseconds;
  }

  function getHoursByDay(day: string): number {
    const workDay = WorkHoursDayWise.value.find((wd: { day: string; }) => wd.day === day);
    return workDay ? workDay.hours : 0; // Default to 0 if not found
  }

  function calculateWorkPercentage(hoursWorked: number, totalHours: number): number {
    const percentage = (hoursWorked / totalHours) * 100;
    return Math.min(percentage, 100); // Ensure it doesn't go over 100%
  }

  function getTotalHoursThisWeek(): number {
    // Get today's day as a string (e.g., "Monday", "Tuesday")
    const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const today = new Date();
    const todayDayName = daysOfWeek[today.getDay()];

    // Calculate the total hours from Monday to today
    let totalHours = 0;
    for (const workDay of WorkHoursDayWise.value) {
      totalHours += workDay.hours;
      if (workDay.day === todayDayName) break; // Stop once we reach today's day
    }

    return totalHours;
  }

  function getTotalWorkingHoursForMonth(): number {
    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();

    let totalHours = 0;

    for (let day = 1; day <= new Date(year, month + 1, 0).getDate(); day++) {
      const currentDate = new Date(year, month, day);
      const dayOfWeek = currentDate.toLocaleDateString('en-US', { weekday: 'long' });
      const workDay = WorkHoursDayWise.value.find(w => w.day === dayOfWeek);
      if (workDay) {
        totalHours += workDay.hours;
      }
    }

    return totalHours;
  }

  // Function to calculate and format elapsed time
  const formatElapsedTime = (startTime: Date) => {
    const now = new Date();
    const diff = now.getTime() - startTime.getTime();

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    return `${hours}h ${minutes}m ${seconds}s`;
  };

  function convertAttendanceToEvents(records: any[]): Event[] {
    const groupedRecords: Record<string, any[]> = {};

    // Group records by date
    records.forEach(record => {
      const date = formatDate(record.created_at);
      if (!groupedRecords[date]) {
        groupedRecords[date] = [];
      }
      groupedRecords[date].push(record);
    });

    // Create events based on grouped data
    return Object.keys(groupedRecords).map(date => {
      const recordsForDay = groupedRecords[date];
      const isPresent = recordsForDay.some(record => record.check_in);

      return {
        id: date,
        title: isPresent ? 'Present' : 'Absent',
        start: new Date(date),
        allDay: true,
        className: isPresent ? 'bg-success-subtle' : 'bg-danger-subtle',
        extendedProps: {
          status: isPresent ? 'Present' : 'Absent',
        },
        description: isPresent ? 'Employee was present' : 'Employee was absent',
      };
    });
  }

  // Helper function to format date to YYYY-MM-DD
  function formatDate(date: string) {
    return dayjs(date).format('YYYY-MM-DD');
  }

// const v$ = useVuelidate();
// const currentEvents = ref<any[]>([]);
const showModal = ref(false);
// const eventModal = ref(false);
// const eventEditModal = ref(false);
// const submitted = ref(false);
// const submit = ref(false);
// const categoriesList = ref(categories);
const newEventData = ref({});
// const edit = ref({});
// const deleteId = ref({});
// const event = ref({
//   title: '',
//   category: '',
//   location: '',
//   descri: '',
//   date: '',
// });
// const editevent = ref({
//   editTitle: '',
//   editcategory: '',
//   editlocation: '',
//   editdescri: '',
//   editdates: '',
//   editcalendardates: '',
// });

// const config = ref({
//   wrap: true,
//   altFormat: 'M j, Y',
//   altInput: true,
//   dateFormat: 'd M, Y',
//   mode: 'range',
// });

// const timeConfig = ref({
//   enableTime: false,
//   altInput: true,
//   dateFormat: 'Z',
//   altFormat: 'd M, Y',
//   mode: 'range',
// });

// const date2 = ref<Date | null>(null);

// const getInitialView = (): string => {
//   if (window.innerWidth >= 768 && window.innerWidth < 1200) {
//     return 'timeGridWeek';
//   } else if (window.innerWidth <= 768) {
//     return 'listMonth';
//   } else {
//     return 'dayGridMonth';
//   }
// };

// const formatDate = (date: Date): string => {
//   const monthNames = [
//     'January', 'February', 'March', 'April', 'May', 'June',
//     'July', 'August', 'September', 'October', 'November', 'December',
//   ];
//   const d = new Date(date);
//   const month = monthNames[d.getMonth()].slice(0, 3);
//   const day = d.getDate().toString().padStart(2, '0');
//   const year = d.getFullYear();
//   return `${day} ${month} ${year}`;
// };

// const dateStamp = (start: Date, end: Date | null): string => {
//   return end ? `${formatDate(start)} - ${formatDate(end)}` : formatDate(start);
// };

// const formatTime = (date: Date): string => {
//   const hours = date.getHours();
//   const minutes = date.getMinutes().toString().padStart(2, '0');
//   const period = hours >= 12 ? 'PM' : 'AM';
//   const hour = hours % 12 || 12;
//   return `${hour}:${minutes} ${period}`;
// };

// const timeStamp = (start: Date, end: Date): string => {
//   return formatTime(start) === formatTime(end) ? 'Full day event' : `${formatTime(start)} - ${formatTime(end)}`;
// };



// const handleSubmit = () => {
//   submitted.value = true;
//   v$.$touch();
//   if (v$.$invalid) return;

//   const calendarApi = calendarOptions.value.getApi();
//   calendarApi.addEvent({
//     id: Math.floor(Math.random() * 100 + 20),
//     title: event.value.title,
//     start: event.value.date.split(' ')[0],
//     end: event.value.date.split(' ')[1],
//     classNames: event.value.category,
//     extendedProps: {
//       department: 'All Day Event',
//       location: event.value.location,
//       description: event.value.descri,
//     },
//   });
//   successmsg();
//   showModal.value = false;
//   newEventData.value = {};
//   submitted.value = false;
//   Object.assign(event.value, { title: '', category: '', location: '', descri: '', date: '' });
// };

// const hideModal = () => {
//   submitted.value = false;
//   showModal.value = false;
//   Object.assign(event.value, { title: '', category: '', location: '', descri: '', date: '' });
// };

// const editSubmit = () => {
//   submit.value = true;
//   edit.value.setProp('title', editevent.value.editTitle);
//   edit.value.setProp('classNames', editevent.value.editcategory);
//   edit.value.setStart(editevent.value.editcalendardates.split(' ')[0]);
//   edit.value.setEnd(editevent.value.editcalendardates.split(' ')[1]);
//   edit.value.setExtendedProp('location', editevent.value.editlocation);
//   edit.value.setExtendedProp('description', editevent.value.editdescri);
//   successmsg();
//   eventModal.value = false;
//   eventEditModal.value = false;
// };

// const deleteEvent = () => {
//   edit.value.remove();
//   eventModal.value = false;
//   eventEditModal.value = false;
// };

const dateClicked = (info: any) => {
  newEventData.value = info;
  showModal.value = true;
};

// const editEvent = (info: any) => {
//   edit.value = info.event;
//   Object.assign(editevent.value, {
//     editTitle: edit.value.title,
//     editcategory: edit.value.classNames,
//     editlocation: edit.value.extendedProps.location,
//     editdescri: edit.value.extendedProps.description,
//     editdates: dateStamp(edit.value.start, edit.value.end),
//     editcalendardates: `${moment(edit.value.start).toISOString()} to ${moment(edit.value.end || edit.value.start).toISOString()}`,
//   });
//   eventModal.value = true;
// };

// const closeModal = () => {
//   eventModal.value = false;
//   eventEditModal.value = false;
// };

// const confirm = () => {
//   Swal.fire({
//     title: 'Are you sure?',
//     text: "You won't be able to delete this!",
//     icon: 'warning',
//     showCancelButton: true,
//     confirmButtonColor: '#34c38f',
//     cancelButtonColor: '#f46a6a',
//     confirmButtonText: 'Yes, delete it!',
//   }).then((result) => {
//     if (result.value) {
//       deleteEvent();
//       Swal.fire('Deleted!', 'Event has been deleted.', 'success');
//     }
//   });
// };

// const handleEvents = (events: any) => {
//   currentEvents.value = events.reverse();
// };

// const successmsg = () => {
//   Swal.fire({
//     position: 'center',
//     icon: 'success',
//     title: 'Event has been saved',
//     showConfirmButton: false,
//     timer: 1000,
//   });
// };


</script>

<template>
  <Layout>
    <Head title="Attendance" />
    <PageHeader title="Attendance" pageTitle="HR" />
    <BRow>
      <BCol cols="12">
        <BRow>
          <BCol xl="3">
            <BCard no-body class="card-h-100">
              <BCardBody>
                <section class="timesheet">
                  <h3>Timesheet {{ new Date().toLocaleDateString() }}</h3>
                  <p>Current Time: {{ new Date().toLocaleString() }}</p>
                  <div v-if="isPunchedIn">
                    <p>Check in at {{ checkInTimeDisplay }}</p>
                    <p>Elapsed time since check-in</p>
                    <div v-if="isPunchedIn" class="time-circle">
                      <div>
                        <!-- <p>{{ formattedCheckInTime }}</p> -->
                        <p>{{ elapsedTime }}</p>
                      </div>
                      <!-- <p>{{ hoursToday }} hrs</p> -->
                    </div>
                  </div>
                  <div v-else>
                    <p>Duration: {{ duration?.hours }} hours: {{ duration?.minutes }} minutes: {{ duration?.seconds }} seconds</p>
                  </div>
                  <BButton variant="primary" class="w-100" id="btn-check-in-out" @click="togglePunch">
                    {{ isPunchedIn ? 'Check Out' : 'Check In' }}
                  </BButton>
                  <div class="break-overtime">
                    <!-- <p>Break: 1.21 hrs</p> -->
                    <p>Overtime: {{ overtimeHours }} hrs</p>
                  </div>
                </section>
              </BCardBody>
            </BCard>
            <!-- Statistics Section -->
            <BCard no-body class="card-h-100">
              <BCardBody>
                <h3>Statistics</h3>
                <div class="live-preview">
                  <BCard no-body class="bg-light overflow-hidden shadow-none">
                    <BCardBody>
                      <div class="d-flex">
                        <div class="flex-grow-1">
                          <h6 class="mb-0">
                            <b class="text-secondary">{{ percentageForToday.toFixed(2) }}%</b> Today...
                          </h6>
                        </div>
                        <div class="flex-shrink-0">
                          <h6 class="mb-0">{{hoursLeftForToday.toFixed(2)}} hours left</h6>
                        </div>
                      </div>
                    </BCardBody>

                    <BProgress class="bg-secondary-subtle rounded-0">
                      <BProgress-bar :value=percentageForToday.toFixed(2) variant="secondary" />
                    </BProgress>
                  </BCard>

                  <BCard no-body class="bg-light overflow-hidden shadow-none">
                    <BCardBody>
                      <div class="d-flex">
                        <div class="flex-grow-1">
                          <h6 class="mb-0">
                            <b class="text-success">{{ percentageForWeek.toFixed(2) }}%</b> This Week...
                          </h6>
                        </div>
                        <div class="flex-shrink-0">
                          <h6 class="mb-0">{{hoursLeftForWeek.toFixed(2)}} hours left</h6>
                        </div>
                      </div>
                    </BCardBody>

                    <BProgress class="bg-success-subtle rounded-0">
                      <BProgress-bar :value="percentageForWeek.toFixed(2)" variant="success" />
                    </BProgress>
                  </BCard>

                  <BCard no-body class="bg-light overflow-hidden shadow-none">
                    <BCardBody>
                      <div class="d-flex">
                        <div class="flex-grow-1">
                          <h6 class="mb-0">
                            <b class="text-danger">{{ percentageForMonth.toFixed(2) }}%</b> This Month...
                          </h6>
                        </div>
                        <div class="flex-shrink-0">
                          <h6 class="mb-0">{{hoursLeftForMonth.toFixed(2)}} hours left</h6>
                        </div>
                      </div>
                    </BCardBody>

                    <BProgress class="bg-danger-subtle rounded-0">
                      <BProgress-bar :value="percentageForMonth.toFixed(2)" variant="danger" />
                    </BProgress>
                  </BCard>
                </div>
                <section class="statistics">
                  <p>Remaining: {{ remainingHours }} hrs</p>
                  <p>Overtime: {{ overtimeHours }} hrs</p>
                </section>
              </BCardBody>
            </BCard>

            <BCard no-body class="card-h-100">
              <BCardBody>
                <h3>Today Activity</h3>
                <BListGroup tag="ul" flush>
                  <BListGroupItem v-for="log in activityLogs" :key="log.time" tag="li">{{ log.action }} at {{ log.time }}</BListGroupItem>
                </BListGroup>
              </BCardBody>
            </BCard>

            <!-- <BCard no-body class="card-h-100">
              <BCardBody>
                <BButton variant="primary" class="w-100" id="btn-new-event" @click="showModal = true">
                  <i class="mdi mdi-plus"></i> Create New Event
                </BButton>

                <div id="external-events">
                  <br />
                  <p class="text-muted">
                    Drag and drop your event or click in the calendar
                  </p>
                  <div class="external-event fc-event bg-success-subtle text-success" data-class="bg-success-subtle">
                    <i class="mdi mdi-checkbox-blank-circle me-2"></i>New Event Planning
                  </div>
                  <div class="external-event fc-event bg-info-subtle text-info" data-class="bg-info-subtle">
                    <i class="mdi mdi-checkbox-blank-circle me-2"></i>Meeting
                  </div>
                  <div class="external-event fc-event bg-warning-subtle text-warning" data-class="bg-warning-subtle">
                    <i class="mdi mdi-checkbox-blank-circle me-2"></i>Generating Reports
                  </div>
                  <div class="external-event fc-event bg-danger-subtle text-danger" data-class="bg-danger-subtle">
                    <i class="mdi mdi-checkbox-blank-circle me-2"></i>Create New theme
                  </div>
                </div>
              </BCardBody>
            </BCard>
            <div>
              <h5 class="mb-1">Upcoming Events</h5>
              <p class="text-muted">Don't miss scheduled events</p>
              <simpleBar class="upcoming-events pe-2 me-n1 mb-3" data-simplebar="init" style="height: 400px">
                <BCard no-body class="mb-3" v-for="event in currentEvents" :key="event.id">
                  <BCardBody>
                    <div class="d-flex mb-3">
                      <div class="flex-grow-1">
                        <i :class="`mdi mdi-checkbox-blank-circle me-2 ${event.classNames}`"></i><span
                          class="fw-medium">{{ this.dateStamp(event.start, event.end) }}</span>
                      </div>
                      <div class="flex-shrink-0">
                        <BBadge tag="small" variant="primary-subtle" class="bg-primary-subtle text-primary ms-auto">{{
                          this.timeStamp(event.start, event.end)
                        }}</BBadge>
                      </div>
                    </div>
                    <h6 class="card-title fs-16">{{ event.title }}</h6>
                    <p class="text-muted text-truncate-two-lines mb-0">{{ (event.extendedProps &&
                      event.extendedProps.description) ?
                      event.extendedProps.description : "N.A."
                    }}</p>
                  </BCardBody>
                </BCard>
              </simpleBar>
            </div>
            <BCard no-body>
              <BCardBody class="bg-info-subtle">
                <div class="d-flex">
                  <div class="flex-shrink-0">
                    <CalendarIcon class="text-info icon-dual-info"></CalendarIcon>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6 class="fs-15">Welcome to your Calendar!</h6>
                    <p class="text-muted mb-0">
                      Event that applications book will appear here. Click on an
                      event to see the details and manage applicants event.
                    </p>
                  </div>
                </div>
              </BCardBody>
            </BCard> -->
          </BCol>
          <BCol xl="9">
            <BCard no-body class="card-h-100">
              <BCardBody>
                <FullCalendar ref="fullCalendar" :options="calendarOptions" />
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
        <div style="clear: both"></div>
      </BCol>
    </BRow>
    <v-dialog v-model="isModalOpen" max-width="500px">
    <template v-slot:default>
      <v-card>
        <v-card-title>
          Attendance Details for {{ selectedAttendance.date }}
        </v-card-title>
        <v-card-text>
          <div v-if="selectedAttendance">
            <p><strong>Check In:</strong> {{ selectedAttendance.checkIn || 'N/A' }}</p>
            <p><strong>Check Out:</strong> {{ selectedAttendance.checkOut || 'N/A' }}</p>
            
            <!-- Regularization Form -->
            <v-form>
              <v-text-field
                v-model="selectedAttendance.checkIn"
                label="Update Check In Time"
                type="time"
              ></v-text-field>
              <v-text-field
                v-model="selectedAttendance.checkOut"
                label="Update Check Out Time"
                type="time"
              ></v-text-field>
              <v-btn color="primary" @click="updateAttendance">Save</v-btn>
            </v-form>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-btn text @click="isModalOpen = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </template>
  </v-dialog>
    <!-- <BModal v-model="showModal" title="Add New Event" body-class="p-4"
      header-class="p-3 bg-info-subtle" hide-footer class="v-modal-custom" centered>
      <form @submit.prevent="handleSubmit" name="event-form" id="form-event">
        <div class="text-end">
          <a href="#" class="btn btn-sm btn-soft-primary" id="edit-event-btn" data-id="new-event"
            onclick="editEvent(this)" role="button" hidden="true">Edit</a>
        </div>
        <div class="row event-form">
          <div class="col-12">
            <div class="mb-3">
              <label class="form-label">Type</label>
              <select v-model="event.category" class="form-control" name="category"
                :class="{ 'is-invalid': submitted && v$.event.category.errors }">
                <option v-for="option in categories" :key="option.backgroundColor" :value="`${option.value}`">
                  {{ option.name }}
                </option>
              </select>
              <div v-if="submitted && v$.event.category.$error" class="invalid-feedback">
                <span v-if="v$.event.category.required.$message">{{
                  v$.event.category.required.$message
                }}</span>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <label class="form-label">Event Name</label>
              <input id="name" v-model="event.title" type="text" class="form-control" placeholder="Insert Event name"
                :class="{ 'is-invalid': submitted && v$.event.title.$error }" />
              <div v-if="submitted && v$.event.title.$error" class="invalid-feedback">
                <span v-if="v$.event.title.required.$message">{{
                  v$.event.title.required.$message
                }}</span>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="mb-3">
              <label>Event Date</label>
              <div class="input-group">
                <flat-pickr placeholder="Select date" v-model="event.date" :config="timeConfig"
                  class="form-control flatpickr-input" id="caledate"></flat-pickr>
                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <label for="event-location">Location</label>
              <div>
                <input type="text" class="form-control d-block" v-model="event.location" name="event-location"
                  id="event-location" placeholder="Event location"
                  :class="{ 'is-invalid': submitted && v$.event.location.$error }">
                <div v-if="submitted && v$.event.location.$error" class="invalid-feedback">
                  <span v-if="v$.event.location.required.$message">{{
                    v$.event.location.required.$message
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <input type="hidden" id="eventid" name="eventid" value="">
          <div class="col-12">
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control d-block" id="event-description" v-model="event.descri"
                placeholder="Enter a description" rows="3" spellcheck="false"
                :class="{ 'is-invalid': submitted && v$.event.descri.$error }"></textarea>
              <div v-if="submitted && v$.event.descri.$error" class="invalid-feedback">
                <span v-if="v$.event.descri.required.$message">{{
                  v$.event.descri.required.$message
                }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="text-end pt-3">
          <BButton variant="light" @click="hideModal">Close</BButton>
          <BButton type="submit" variant="success" class="ms-1">Create event</BButton>
        </div>
      </form>
    </BModal>


    <BModal v-model="eventModal" :title="editevent.value.editTitle" hide-footer
      body-class="p-4" header-class="p-3 bg-info-subtle" class="v-modal-custom" centered>
      <div class="text-end">
        <a href="#" class="btn btn-sm btn-soft-primary" id="edit-event-btn" data-id="edit-event" role="button"
          @click="this.eventEditModal = true, this.eventModal = false">Edit</a>
      </div>
      <div class="event-details">
        <div class="d-flex mb-2">
          <div class="flex-grow-1 d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
              <i class="ri-calendar-event-line text-muted fs-16"></i>
            </div>
            <div class="flex-grow-1">
              <h6 class="d-block fw-semibold mb-0" id="event-start-date-tag">
                {{ this.editevent.editdates }}
              </h6>
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center mb-2">
          <div class="flex-shrink-0 me-3">
            <i class="ri-time-line text-muted fs-16"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="d-block fw-semibold mb-0"><span id="event-timepicker1-tag"></span> - <span
                id="event-timepicker2-tag"></span></h6>
          </div>
        </div>
        <div class="d-flex align-items-center mb-2">
          <div class="flex-shrink-0 me-3">
            <i class="ri-map-pin-line text-muted fs-16"></i>
          </div>
          <div class="flex-grow-1">
            <h6 class="d-block fw-semibold mb-0"> <span id="event-location-tag">
                {{ this.editevent.editlocation || "No Location" }}
              </span></h6>
          </div>
        </div>
        <div class="d-flex mb-3">
          <div class="flex-shrink-0 me-3">
            <i class="ri-discuss-line text-muted fs-16"></i>
          </div>
          <div class="flex-grow-1">
            <p class="d-block text-muted mb-0" id="event-description-tag">
              {{ this.editevent.editdescri || "N.A." }}
            </p>
          </div>
        </div>
      </div>
      <div class="hstack gap-2 justify-content-end">
        <button type="button" class="btn btn-soft-danger" id="btn-delete-event" @click="confirm"><i
            class="ri-close-line align-bottom"></i> Delete</button>
      </div>
    </BModal>

    <BModal v-model="eventEditModal" :title="editevent.value.editTitle" title-class="text-black font-18" body-class="p-4"
      header-class="p-3 bg-info-subtle" hide-footer class="v-modal-custom" centered>
      <form @submit.prevent="editSubmit" name="event-form" id="form-event">
        <div class="row event-form">
          <div class="col-12">
            <div class="mb-3">
              <label class="form-label">Type</label>
              <select v-model="editevent.editcategory" class="form-control" name="category">
                <option v-for="option in categories" :key="option.backgroundColor" :value="`${option.value}`">
                  {{ option.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <label class="form-label">Event Name</label>
              <input id="name" v-model="editevent.value.editTitle" type="text" class="form-control"
                placeholder="Insert Event name" />
            </div>
          </div>

          <div class="col-12">
            <div class="mb-3">
              <label>Event Date</label>
              <div class="input-group">
                <flat-pickr placeholder="Select date" v-model="editevent.editcalendardates" :config="timeConfig"
                  class="form-control flatpickr-input" id="caledate"></flat-pickr>
                <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="mb-3">
              <label for="event-location">Location</label>
              <div>
                <input type="text" class="form-control d-block" v-model="editevent.editlocation" name="event-location"
                  id="event-location" placeholder="Event location" />
              </div>
            </div>
          </div>

          <input type="hidden" id="eventid" name="eventid" value="">
          <div class="col-12">
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control d-block" id="event-description" v-model="editevent.editdescri"
                placeholder="Enter a description" rows="3" spellcheck="false"></textarea>
            </div>
          </div>
        </div>
        <div class="hstack gap-2 justify-content-end">
          <button type="button" class="btn btn-soft-danger" id="btn-delete-event" @click="confirm"><i
              class="ri-close-line align-bottom"></i> Delete</button>
          <button type="submit" class="btn btn-success" id="btn-save-event">Update Event</button>
        </div>
      </form>
    </BModal> -->
  </Layout>
</template>

<style scoped>
.attendance-screen {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.header h2 {
  color: #f56545;
}
.main-content {
  display: flex;
  gap: 1rem;
}
.timesheet, .statistics, .today-activity {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
}
.time-circle {
  /* width: 100px;
  height: 100px; */
  border-radius: 3%;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin: 1rem auto;
  padding: 20px;
}
button {
  background-color: #f56545;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
}
button:hover {
  background-color: #e5533d;
}
.break-overtime {
  display: flex;
  justify-content: space-around;
  margin-top: 0.5rem;
}
.monthly-attendance {
  margin-top: 1rem;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  text-align: left;
}
th {
  background-color: #f9fafb;
}
</style>