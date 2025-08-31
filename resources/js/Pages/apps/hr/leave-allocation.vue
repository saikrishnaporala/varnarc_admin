<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import flatPickr from "vue-flatpickr-component";
import { BriefcaseIcon, AwardIcon, ClockIcon } from '@zhuowenli/vue-feather-icons';
import { ref } from 'vue';
import LeaveAllocationForm from "@/interfaces/LeaveAllocationForm";
import FormErrors from "@/interfaces/FormErrors";
import LeavesWidget from "@/interfaces/LeavesWidget";


// Form data using ref
const leaveAllocationForm = ref<LeaveAllocationForm>({
  employeeId: '',
  employeeName: '',
  leaveType: '',
  totalLeaves: 0,
  allocatedLeaves: 0,
  validFrom: '',
  validTo: '',
});

// Dummy data for employees and leave types (replace this with an API call if needed)
const employees = [
  { id: 'EMP001', name: 'John Doe' },
  { id: 'EMP002', name: 'Jane Smith' },
  { id: 'EMP003', name: 'Alice Johnson' },
];


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

const toggleShow = ref(true);
// Toggle between columns
const toggleColumns = () => {
  toggleShow.value = !toggleShow.value
}

// Function to validate the form
const validateForm = (): boolean => {
  errors.value = {}; // Reset errors

  if (!leaveAllocationForm.value.employeeId) {
    errors.value.employeeId = 'Employee is required.';
  }
  if (!leaveAllocationForm.value.leaveType) {
    errors.value.leaveType = 'Leave type is required.';
  }
  if (leaveAllocationForm.value.totalLeaves <= 0) {
    errors.value.totalLeaves = 'Total leaves must be greater than 0.';
  }
  if (leaveAllocationForm.value.allocatedLeaves <= 0) {
    errors.value.allocatedLeaves = 'Allocated leaves must be greater than 0.';
  }
  if (!leaveAllocationForm.value.validFrom) {
    errors.value.validFrom = 'Start date is required.';
  }
  if (!leaveAllocationForm.value.validTo) {
    errors.value.validTo = 'End date is required.';
  }

  return Object.keys(errors.value).length === 0;
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
      body: JSON.stringify(leaveAllocationForm.value),
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
    <Head title="Leave Allocation" />
    <PageHeader title="Leave Allocation" pageTitle="HR" />
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
              <BButton variant="primary" @click="toggleColumns">
                <i class="ri-add-fill me-1 align-bottom"></i> Add Leave Allocation
              </BButton>
            </div>
          </BCol>
        </BRow>
      </BCardBody>
    </Bcard>
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="4" v-if="toggleShow">
            <BCard no-body>
              <BCardHeader class="border-0">
                <BCardTitle class="mb-0">Leave Allocation</BCardTitle>
              </BCardHeader>
              <BCardBody class="pt-0">
                <form @submit.prevent="submitForm">
                  <!-- Select Employee -->
                  <div class="mb-3">
                    <label for="employeeId" class="form-label">Select Employee</label>
                    <select id="employeeId" class="form-select" v-model="leaveAllocationForm.employeeId">
                      <option value="">Select Employee</option>
                      <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                        {{ employee.name }}
                      </option>
                    </select>
                    <div v-if="errors.employeeId" class="text-danger">{{ errors.employeeId }}</div>
                  </div>

                  <!-- Leave Type -->
                  <div class="mb-3">
                    <label for="leaveType" class="form-label">Leave Type</label>
                    <select id="leaveType" class="form-select" v-model="leaveAllocationForm.leaveType">
                      <option value="">Select Leave Type</option>
                      <option v-for="type in leavesWidgets" :key="type">{{ type.label }}</option>
                    </select>
                    <div v-if="errors.leaveType" class="text-danger">{{ errors.leaveType }}</div>
                  </div>

                  <!-- Total Leaves -->
                  <div class="mb-3">
                    <label for="totalLeaves" class="form-label">Total Leaves</label>
                    <input type="number" id="totalLeaves" class="form-control" v-model.number="leaveAllocationForm.totalLeaves" />
                    <div v-if="errors.totalLeaves" class="text-danger">{{ errors.totalLeaves }}</div>
                  </div>

                  <!-- Allocated Leaves -->
                  <div class="mb-3">
                    <label for="allocatedLeaves" class="form-label">Allocated Leaves</label>
                    <input type="number" id="allocatedLeaves" class="form-control" v-model.number="leaveAllocationForm.allocatedLeaves" />
                    <div v-if="errors.allocatedLeaves" class="text-danger">{{ errors.allocatedLeaves }}</div>
                  </div>

                  <!-- Valid From -->
                  <div class="mb-3">
                    <label for="validFrom" class="form-label">Valid From</label>
                    <input type="date" id="validFrom" class="form-control" v-model="leaveAllocationForm.validFrom" />
                    <div v-if="errors.validFrom" class="text-danger">{{ errors.validFrom }}</div>
                  </div>

                  <!-- Valid To -->
                  <div class="mb-3">
                    <label for="validTo" class="form-label">Valid To</label>
                    <input type="date" id="validTo" class="form-control" v-model="leaveAllocationForm.validTo" />
                    <div v-if="errors.validTo" class="text-danger">{{ errors.validTo }}</div>
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
                <BCardTitle class="mb-0">Employee Leave Allocations</BCardTitle>
              </BCardHeader>
              <BCardBody class="pt-0">
                <table class="table table-bordered table-striped mt-3">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Employee ID</th>
                      <th>Employee Name</th>
                      <th>Leave Types</th>
                      <th>Total Leaves</th>
                      <th>Allocated Leaves</th>
                      <th>Validity Period</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(employee, index) in employeesLeaveAllocation" :key="employee.employeeId">
                      <td>{{ index + 1 }}</td>
                      <td>{{ employee.employeeId }}</td>
                      <td>{{ employee.employeeName }}</td>
                      <td>
                        <div v-for="allocation in employee.leaveAllocations" :key="allocation.leaveType">
                          <strong>{{ allocation.leaveType }}</strong>
                        </div>
                      </td>
                      <td>
                        <div v-for="allocation in employee.leaveAllocations" :key="allocation.leaveType">
                          {{ allocation.totalLeaves }}
                        </div>
                      </td>
                      <td>
                        <div v-for="allocation in employee.leaveAllocations" :key="allocation.leaveType">
                          {{ allocation.allocatedLeaves }}
                        </div>
                      </td>
                      <td>
                        <div v-for="allocation in employee.leaveAllocations" :key="allocation.leaveType">
                          {{ allocation.validFrom }} - {{ allocation.validTo }}
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </BCardBody>
            </BCard>
          </BCol>
        </BRow>
        <!-- <BRow>
          <BCol xl="12">
            <Project />
          </BCol>
        </BRow> -->
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