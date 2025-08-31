<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import flatPickr from "vue-flatpickr-component";
import { ref } from 'vue';
import LeaveAllocationForm from "@/interfaces/LeaveAllocationForm";
import FormErrors from "@/interfaces/FormErrors";
import Holiday from "@/interfaces/Holiday";
import { icons } from "@/utils/data-material";

interface Icon {
  name: string;
  hex: string;
  version: string;
}

const formData = ref<Holiday>({ id: 0, name: '', date: '', description: '' });


const holidays = ref<Holiday[]>([]);
const isEditing = ref(false);
const editingIndex = ref<number | null>(null);

// Sample data for holidays
holidays.value = [
  { name: 'New Year\'s Day', date: '2024-01-01', description: 'Celebration of the New Year', icon: 'calendar-star' },
  { name: 'Independence Day', date: '2024-07-04', description: 'National holiday in the USA', icon: 'flag' },
  { name: 'Labor Day', date: '2024-09-02', description: 'Honoring the labor force', icon: 'hammer' },
  { name: 'Christmas Day', date: '2024-12-25', description: 'Christmas celebrations', icon: 'christmas-tree' },
  { name: 'Thanksgiving', date: '2024-11-28', description: 'Day of gratitude', icon: 'turkey' }
];

const selectedIcon = ref<Icon>(icons[0]); // Default icon
const isOpen = ref(false);
let nextId = 1;

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const selectIcon = (icon: Icon) => {
  selectedIcon.value = icon;
  isOpen.value = false;
};

// Form Validation Function
function validateForm(): boolean {
  errors.value = {};
  if (!formData.value.name) errors.value.name = 'Name is required';
  if (!formData.value.date) errors.value.date = 'Date is required';
  if (!formData.value.description) errors.value.description = 'Description is required';

  return Object.keys(errors.value).length === 0;
}

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

// Handle form submission
const handleSubmit = () => {
  if (!validateForm()) return;

  if (isEditing.value) {
    // Update existing holiday
    const index = holidays.value.findIndex(h => h.id === formData.value.id);
    if (index !== -1) {
      holidays.value[index] = { ...formData.value };
    }
    isEditing.value = false;
  } else {
    // Add new holiday
    holidays.value.push({ ...formData.value, id: nextId++ });
  }

  resetForm();
};

// Reset form
const resetForm = () => {
  formData.value = { leaveType: '', icon: 'fa-solid fa-calendar' };
  editingIndex.value = null;
  isEditing.value = false;
};

// Edit holiday
function editHoliday(holiday: Holiday) {
  formData.value = { ...holiday };
  isEditing.value = true;
}

// Delete holiday
function deleteHoliday(id: number) {
  holidays.value = holidays.value.filter(h => h.id !== id);
}
</script>

<template>
  <Layout>
    <Head title="Holidays" />
    <PageHeader title="Holidays" pageTitle="HR" />
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
          <BCol xl="4">
            <BCard no-body>
              <BCardHeader class="border-0">
                <BCardTitle class="mb-0">Holiday</BCardTitle>
              </BCardHeader>
              <BCardBody class="pt-0">
                <div v-if="submitted" class="confirmation-message">
                  <p>Thank you, {{ formData.employeeName }}! Your request has been submitted.</p>
                </div>
                <form @submit.prevent="submitForm">
                  <div class="mb-3">
                    <label for="name">Holiday Name</label>
                    <input type="text" id="name" v-model="formData.name" class="form-control" :class="{ 'is-invalid': errors.name }"/>
                    <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
                  </div>

                  <div class="mb-3">
                    <label for="date">Holiday Date</label>
                    <input type="date" id="date" v-model="formData.date" class="form-control" :class="{ 'is-invalid': errors.date }"/>
                    <span v-if="errors.date" class="text-danger">{{ errors.date }}</span>
                  </div>

                  <div class="mb-3">
                    <label for="description">Description</label>
                    <textarea id="description" v-model="formData.description" class="form-control" :class="{ 'is-invalid': errors.description }"></textarea>
                    <span v-if="errors.description" class="text-danger">{{ errors.description }}</span>
                  </div>

                  <!-- Submit Button -->
                  <BCol lg="12">
                    <div class="hstack gap-2 justify-content-end">
                      <BButton type="submit" variant="primary">{{ isEditing ? 'Update ' : 'Add ' }}Holiday</BButton>
                    </div>
                  </BCol>
                </form>
              </BCardBody>
            </BCard>
          </BCol>
          <BCol xl="8">
            <BCard no-body>
              <BCardHeader class="border-0">
                <BCardTitle class="mb-0">Holidays</BCardTitle>
              </BCardHeader>
              <BCardBody class="pt-0">
                <table class="table table-bordered table-striped mt-3">
                  <thead>
                    <tr>
                      <th>Icon</th>
                      <th>Holiday Name</th>
                      <th>Date</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(holiday, index) in holidays" :key="index">
                      <td>
                        <!-- Display the icon if it exists -->
                        <i v-if="holiday.icon" :class="'mdi mdi-' + holiday.icon"></i>
                      </td>
                      <td>{{ holiday.name }}</td>
                      <td>{{ holiday.date }}</td>
                      <td>{{ holiday.description }}</td>
                      <td>
                        <div class="hstack gap-3 fs-15">
                            <a href="javascript:void(0);" @click="editLeaveType(index)" class="link-primary"><i class="ri-settings-4-line"></i></a>
                            <a href="javascript:void(0);" @click="deleteLeaveType(index)" class="link-danger"><i class="ri-delete-bin-5-line"></i></a>
                        </div>
                      </td>
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