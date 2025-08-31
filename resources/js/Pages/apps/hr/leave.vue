<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import flatPickr from "vue-flatpickr-component";
import { BriefcaseIcon, AwardIcon, ClockIcon } from '@zhuowenli/vue-feather-icons';
import { ref } from 'vue';
import LeaveApplicationForm from "@/interfaces/LeaveApplicationForm";
import FormErrors from "@/interfaces/FormErrors";
import LeavesWidget from "@/interfaces/LeavesWidget";


const leaveForm = ref<LeaveApplicationForm>({
  employeeId: 'OPTY016',
  employeeName: 'Sai Krishna P',
  leaveType: '',
  startDate: null,
  endDate: null,
  teamEmail: '',
  reason: '',
  approverId: ''
});

const leavesWidgets: LeavesWidget[] = [
  { id: 1, availed: 0, booked: 0, label: "Casual Leave", icon: "las la-calendar" },
  { id: 2, availed: 0, booked: 0, label: "Compensatory Leave", icon: "las la-calendar-plus" },
  { id: 3, availed: 0, booked: 0, label: "Earned Leave", icon: "las la-user-plus" },
  { id: 4, availed: 0, booked: 0, label: "Family and Medical Leave", icon: "las la-clinic-medical" },
  { id: 5, availed: 0, booked: 0, label: "Personal Leave", icon: "mdi mdi-calendar-question" },
  { id: 6, availed: 0, booked: 0, label: "Vacation", icon: "las la-umbrella-beach" },
  { id: 7, availed: 0, booked: 0, label: "Maternity Leave", icon: "las la-user-md" },
  { id: 8, availed: 0, booked: 0, label: "Paternity Leave", icon: "las la-hospital" }
];

// Define the date state using ref
const date = ref<string | null>(null);

// Define the configuration object for the flatPickr component
const config = ref({
  inline: true,
});

// Year Navigation
const currentYear = new Date().getFullYear();
const yearRange = ref(`${currentYear}-01-01 - ${currentYear}-12-31`);

const prevYear = () => {
  const year = parseInt(yearRange.value.slice(0, 4)) - 1;
  yearRange.value = `${year}-01-01 - ${year}-12-31`;
};

const nextYear = () => {
  const year = parseInt(yearRange.value.slice(0, 4)) + 1;
  yearRange.value = `${year}-01-01 - ${year}-12-31`;
};

// Reactive errors object
const errors = ref<FormErrors>({});

// Function to validate the form
const validateForm = (): boolean => {
  errors.value = {}; // Reset errors

  if (!leaveForm.value.leaveType) errors.value.leaveType = 'Leave type is required.';
  if (!leaveForm.value.startDate) errors.value.startDate = 'Start date is required.';
  if (!leaveForm.value.endDate) errors.value.endDate = 'End date is required.';
  if (!leaveForm.value.teamEmail) {
    errors.value.teamEmail = 'Team email is required.';
  } else if (!/\S+@\S+\.\S+/.test(leaveForm.value.teamEmail)) {
    errors.value.teamEmail = 'Invalid email format.';
  }
  if (!leaveForm.value.reason) errors.value.reason = 'Reason is required.';
  if (!leaveForm.value.approverId) errors.value.approverId = 'Approver is required.';

  return Object.keys(errors.value).length === 0;
};

// Function to handle form submission
const submitForm = async () => {
  if (!validateForm()) {
    console.log('Validation failed:', errors.value);
    return;
  }

  try {
    const response = await fetch('/api/leave-application', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(leaveForm.value),
    });

    if (!response.ok) {
      throw new Error('Failed to submit leave application');
    }

    const result = await response.json();
    console.log('Leave application submitted successfully:', result);
    alert('Leave application submitted successfully!');
  } catch (error) {
    console.error('Error submitting form:', error);
    alert('Failed to submit leave application.');
  }
};

</script>

<template>
  <Layout>
    <Head title="Leaves" />
    <PageHeader title="Leaves" pageTitle="HR" />
    <BCard no-body>
      <BCardBody>
        <BRow class="g-2">
          <BCol sm="auto" class="ms-auto">
            <nav>
              <ul class="pagination justify-content-center align-items-center mb-0">
                <li class="page-item">
                  <a class="page-link" href="#" @click.prevent="prevYear">
                    <i class="mdi mdi-arrow-left-bold"></i>
                  </a>
                </li>
                <li class="page-item disabled">
                  <span class="page-link">{{ yearRange }}</span>
                </li>
                <li class="page-item">
                  <a class="page-link" href="#" @click.prevent="nextYear">
                    <i class="mdi mdi-arrow-right-bold"></i>
                  </a>
                </li>
              </ul>
            </nav>
          </BCol>
          <BCol sm="auto" class="ms-auto">
            <div class="list-grid-nav hstack gap-1">
              <BButton variant="primary" @click="modalShow2 = !modalShow2">
                <i class="ri-add-fill me-1 align-bottom"></i> Apply Leave
              </BButton>
            </div>
          </BCol>
        </BRow>
      </BCardBody>
    </Bcard>
    <BRow class="project-wrapper">
      <BCol xxl="9">
        <BRow>
          <BCol xl="3">
            <BListGroup v-for="(item, index) of leavesWidgets" :key="index">
              <BListGroupItem tag="li" class="mb-2">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <div class="d-flex">
                      <div class="flex-shrink-0 avatar-xs">
                        <div class="avatar-title bg-danger-subtle text-danger rounded">
                          <i :class="item.icon"></i>
                        </div>
                      </div>
                      <div class="flex-shrink-0 ms-2" style="width: 80%;">
                        <h6 class="fs-md mb-0 text-wrap">{{ item.label }}</h6>
                      </div>
                    </div>
                  </div>
                  <div class="flex-shrink-0">
                    <span class="text-success">Available : {{ item.availed }}</span><br>
                    <span class="text-danger">Booked : {{ item.booked }}</span>
                  </div>
                </div>
              </BListGroupItem>
            </BListGroup>
          </BCol>
          <BCol xl="6">
            <BCard no-body>
              <BCardHeader class="border-0">
                <BCardTitle class="mb-0">Leave Application</BCardTitle>
              </BCardHeader>
              <BCardBody class="pt-0">
                <form @submit.prevent="submitForm">
                  <!-- Employee ID -->
                  <div class="mb-3">
                    <label for="employeeId" class="form-label">Employee ID</label>
                    <input type="text" id="employeeId" class="form-control" v-model="leaveForm.employeeId" readonly />
                  </div>

                  <!-- Leave Type -->
                  <div class="mb-3">
                    <label for="leaveType" class="form-label">Leave Type</label>
                    <select id="leaveType" class="form-select" v-model="leaveForm.leaveType">
                      <option value="">Select</option>
                      <option value="Sick Leave">Sick Leave</option>
                      <option value="Casual Leave">Casual Leave</option>
                      <option value="Earned Leave">Earned Leave</option>
                    </select>
                    <div v-if="errors.leaveType" class="text-danger">{{ errors.leaveType }}</div>
                  </div>

                  <!-- Date Range -->
                  <div class="mb-3">
                    <label class="form-label">Date</label>
                    <div class="d-flex">
                      <input type="date" class="form-control me-2" v-model="leaveForm.startDate" />
                      <input type="date" class="form-control" v-model="leaveForm.endDate" />
                    </div>
                    <div v-if="errors.startDate" class="text-danger">{{ errors.startDate }}</div>
                    <div v-if="errors.endDate" class="text-danger">{{ errors.endDate }}</div>
                  </div>

                  <!-- Team Email -->
                  <div class="mb-3">
                    <label for="teamEmail" class="form-label">Team Email ID</label>
                    <input type="email" id="teamEmail" class="form-control" v-model="leaveForm.teamEmail" />
                    <div v-if="errors.teamEmail" class="text-danger">{{ errors.teamEmail }}</div>
                  </div>

                  <!-- Reason for Leave -->
                  <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Leave</label>
                    <textarea id="reason" class="form-control" rows="3" v-model="leaveForm.reason"></textarea>
                    <div v-if="errors.reason" class="text-danger">{{ errors.reason }}</div>
                  </div>

                  <!-- Approver -->
                  <div class="mb-3">
                    <label for="approverId" class="form-label">Select Approver</label>
                    <select id="approverId" class="form-select" v-model="leaveForm.approverId">
                      <option value="">Select</option>
                      <option value="manager01">Manager 1</option>
                      <option value="manager02">Manager 2</option>
                    </select>
                    <div v-if="errors.approverId" class="text-danger">{{ errors.approverId }}</div>
                  </div>

                  <!-- Submit Button -->
                  <BCol lg="12">
                    <div class="hstack gap-2 justify-content-end">
                      <BButton type="button" variant="light" @click="modalShow2 = false">Close</BButton>
                      <BButton type="submit" variant="primary">Submit</BButton>
                    </div>
                  </BCol>
                </form>
              </BCardBody>
            </BCard>
          </BCol>
          <BCol xl="4">
          </BCol>
        </BRow>
        <!-- <BRow>
          <BCol xl="12">
            <Project />
          </BCol>
        </BRow> -->
      </BCol>

      <BCol xxl="3">
        <BCard no-body>
          <BCardHeader class="border-0">
            <BCardTitle class="mb-0">Upcoming Schedules</BCardTitle>
          </BCardHeader>
          <BCardBody class="pt-0">
            <div class="upcoming-scheduled">
              <flat-pickr v-model="date" :config="config"></flat-pickr>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>

    <!-- <BRow>
      <BCol xl="7">
        <ActiveProjects />
      </BCol>
      <BCol xl="5">
        <Task />
      </BCol>
    </BRow>

    <BRow>
      <BCol xxl="4">
        <Member />
      </BCol>
      <BCol xxl="4" lg="6">
        <Chat />
      </BCol>
      <BCol xxl="4" lg="6">
        <Status />
      </BCol>
    </BRow> -->

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