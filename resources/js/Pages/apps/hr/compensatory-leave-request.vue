<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import flatPickr from "vue-flatpickr-component";
import { BriefcaseIcon, AwardIcon, ClockIcon } from '@zhuowenli/vue-feather-icons';
import { ref } from 'vue';
import LeaveAllocationForm from "@/interfaces/LeaveAllocationForm";
import FormErrors from "@/interfaces/FormErrors";
import CompensatoryLeaveRequest from "@/interfaces/CompensatoryLeaveRequest";


const formData = ref<CompensatoryLeaveRequest>({
  employeeName: '',
  employeeId: '',
  dateWorked: '',
  leaveDate: '',
  reason: ''
})

// Dummy data for employees and leave types (replace this with an API call if needed)
const employees = [
  { id: 'EMP001', name: 'John Doe' },
  { id: 'EMP002', name: 'Jane Smith' },
  { id: 'EMP003', name: 'Alice Johnson' },
];

const leaveRequests = ref<CompensatoryLeaveRequest[]>([]);

const toggleShow = ref(true);
// Toggle between columns
const toggleColumns = () => {
  toggleShow.value = !toggleShow.value
}

// Validation function
const validateForm = (): boolean => {
  errors.value = {};
  let isValid = true;

  // Employee Name validation
  if (!formData.value.employeeName) {
    errors.value.employeeName = 'Employee Name is required';
    isValid = false;
  }

  // Employee ID validation
  if (!formData.value.employeeId) {
    errors.value.employeeId = 'Employee ID is required';
    isValid = false;
  }

  // Date Worked validation
  if (!formData.value.dateWorked) {
    errors.value.dateWorked = 'Date Worked is required';
    isValid = false;
  } else if (new Date(formData.value.dateWorked) > new Date()) {
    errors.value.dateWorked = 'Date Worked cannot be in the future';
    isValid = false;
  }

  // Leave Date validation
  if (!formData.value.leaveDate) {
    errors.value.leaveDate = 'Leave Date is required';
    isValid = false;
  } else if (new Date(formData.value.leaveDate) < new Date(formData.value.dateWorked)) {
    errors.value.leaveDate = 'Leave Date cannot be before Date Worked';
    isValid = false;
  }

  // Reason validation
  if (!formData.value.reason) {
    errors.value.reason = 'Reason is required';
    isValid = false;
  }

  return isValid;
};

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
const submitted = ref(false);

// Function to handle form submission
const submitForm = async () => {
  if (!validateForm()) {
    console.log('Validation failed:', errors.value);
    return;
  }

  try {
    const response = await fetch('/api/leave-allocation', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(formData.value),
    });

    if (!response.ok) {
      throw new Error('Failed to allocate leave');
    }

    const result = await response.json();
    console.log('Leave allocation submitted successfully:', result);
    alert('Leave allocation submitted successfully!');
  } catch (error) {
    console.error('Error submitting form:', error);
    alert('Failed to allocate leave.');
  }
};

// Sample data
const employeesLeaveAllocation = ref<LeaveAllocationForm[]>([
  {
    employeeId: 'EMP001',
    employeeName: 'John Doe',
    leaveAllocations: [
      {
        leaveType: 'Annual Leave',
        totalLeaves: 20,
        allocatedLeaves: 15,
        validFrom: '2024-01-01',
        validTo: '2024-12-31',
      },
      {
        leaveType: 'Sick Leave',
        totalLeaves: 10,
        allocatedLeaves: 4,
        validFrom: '2024-01-01',
        validTo: '2024-12-31',
      },
    ],
  },
  {
    employeeId: 'EMP002',
    employeeName: 'Jane Smith',
    leaveAllocations: [
      {
        leaveType: 'Annual Leave',
        totalLeaves: 18,
        allocatedLeaves: 10,
        validFrom: '2024-01-01',
        validTo: '2024-12-31',
      },
      {
        leaveType: 'Maternity Leave',
        totalLeaves: 90,
        allocatedLeaves: 45,
        validFrom: '2024-01-01',
        validTo: '2024-12-31',
      },
    ],
  },
]);

</script>

<template>
  <Layout>
    <Head title="Compensatory Leave Request" />
    <PageHeader title="Compensatory Leave Request" pageTitle="HR" />
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
          <!-- <BCol sm="auto" class="ms-auto">
            <div class="list-grid-nav hstack gap-1">
              <BButton variant="primary" @click="toggleColumns">
                <i class="ri-add-fill me-1 align-bottom"></i> Add Leave Allocation
              </BButton>
            </div>
          </BCol> -->
        </BRow>
      </BCardBody>
    </Bcard>
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="4" v-if="toggleShow">
            <BCard no-body>
              <BCardHeader class="border-0">
                <BCardTitle class="mb-0">Compensatory Leave Request</BCardTitle>
              </BCardHeader>
              <BCardBody class="pt-0">
                <div v-if="submitted" class="confirmation-message">
                  <p>Thank you, {{ formData.employeeName }}! Your request has been submitted.</p>
                </div>
                <form @submit.prevent="submitForm">
                  <!-- Employee Name -->
                  <div class="mb-3">
                    <label for="employeeId" class="form-label">Select Employee</label>
                    <select id="employeeId" class="form-select" v-model="formData.employeeId">
                      <option value="">Select Employee</option>
                      <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                        {{ employee.name }}
                      </option>
                    </select>
                    <div v-if="errors.employeeId" class="text-danger">{{ errors.employeeId }}</div>
                  </div>
                  <div class="mb-3">
                    <label for="name" class="form-label">Employee Name:</label>
                    <input id="name" class="form-control" v-model="formData.name" type="text" placeholder="Enter your name" :class="{ 'is-invalid': errors.name }" />
                    <div v-if="errors.name" class="text-danger">{{ errors.name }}</div>
                  </div>

                  <!-- Employee ID -->
                  <div class="mb-3">
                    <label for="employeeId" class="form-label">Employee ID:</label>
                    <input id="employeeId" class="form-control" v-model="formData.employeeId" type="text" placeholder="Enter your employee ID" :class="{ 'is-invalid': errors.employeeId }"/>
                    <div v-if="errors.employeeId" class="text-danger">{{ errors.employeeId }}</div>
                  </div>

                  <!-- Date Worked -->
                  <div class="mb-3">
                    <label for="dateWorked" class="form-label">Date Worked:</label>
                    <input id="dateWorked" class="form-control" v-model="formData.dateWorked" type="date" :class="{ 'is-invalid': errors.dateWorked }"/>
                    <div v-if="errors.dateWorked" class="text-danger">{{ errors.dateWorked }}</div>
                  </div>
                  
                  <!-- Leave Date -->
                  <div class="mb-3">
                    <label for="leaveDate" class="form-label">Compensatory Leave Date:</label>
                    <input id="leaveDate" class="form-control" v-model="formData.leaveDate" type="date" :class="{ 'is-invalid': errors.leaveDate }"/>
                    <div v-if="errors.leaveDate" class="text-danger">{{ errors.leaveDate }}</div>
                  </div>

                  <!-- Reason -->
                  <div class="mb-3">
                    <label for="reason" class="form-label">Reason:</label>
                    <textarea id="reason" class="form-control" v-model="formData.reason" placeholder="Enter reason" :class="{ 'is-invalid': errors.reason }"></textarea>
                    <div v-if="errors.reason" class="text-danger">{{ errors.reason }}</div>
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
          <BCol xl="8" v-if="toggleShow">
            <BCard no-body>
              <BCardHeader class="border-0">
                <BCardTitle class="mb-0">Employee Compensatory Leave Submitted Requests</BCardTitle>
              </BCardHeader>
              <BCardBody class="pt-0">
                <table class="table table-bordered table-striped mt-3">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Employee Name</th>
                      <th>Employee ID</th>
                      <th>Date Worked</th>
                      <th>Leave Date</th>
                      <th>Reason</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(request, index) in employeesLeaveAllocation" :key="request.employeeId">
                      <td>{{ index + 1 }}</td>
                      <td>{{ request.employeeName }}</td>
                      <td>{{ request.employeeId }}</td>
                      <td>{{ request.dateWorked }}</td>
                      <td>{{ request.leaveDate }}</td>
                      <td>{{ request.reason }}</td>
                    </tr>
                  </tbody>
                </table>
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
      </BCol>
    </BRow>
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