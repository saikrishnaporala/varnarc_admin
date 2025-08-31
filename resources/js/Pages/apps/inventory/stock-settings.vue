<script lang="ts" setup>
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import flatPickr from "vue-flatpickr-component";
import { ref } from 'vue';
import LeaveAllocationForm from "@/interfaces/LeaveAllocationForm";
import FormErrors from "@/interfaces/FormErrors";
import LeaveType from "@/interfaces/LeaveType";
import { icons } from "@/utils/data-material";

interface Icon {
  name: string;
  hex: string;
  version: string;
}

const formData = ref<LeaveType>({
  leaveType: '',
  leaveDays: '',
  icon: 'fa-solid fa-calendar',
});

const leaveTypes = ref<LeaveType[]>([]);
const isEditing = ref(false);
const editingIndex = ref<number | null>(null);

// Sample data for leave types
leaveTypes.value = [
  { leaveType: 'Annual Leave', leaveDays: '10', icon: 'calendar' },
  { leaveType: 'Sick Leave', leaveDays: '10', icon: 'hospital' },
  { leaveType: 'Casual Leave', leaveDays: '10', icon: 'umbrella' },
  { leaveType: 'Maternity Leave', leaveDays: '10', icon: 'baby-bottle' },
  { leaveType: 'Compensatory Leave', leaveDays: '10', icon: 'clock' }
];

const selectedIcon = ref<Icon>(icons[0]); // Default icon
const isOpen = ref(false);

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const selectIcon = (icon: Icon) => {
  selectedIcon.value = icon;
  isOpen.value = false;
};

// Validation function
const validateForm = (): boolean => {
  errors.value = {};
  if (!formData.value.leaveType) {
    errors.value.leaveType = 'Leave Type is required';
    return false;
  }
  return true;
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

// Handle form submission
const handleSubmit = () => {
  if (validateForm()) {
    if (isEditing.value && editingIndex.value !== null) {
      leaveTypes.value[editingIndex.value] = { ...formData.value };
      isEditing.value = false;
    } else {
      leaveTypes.value.push({ ...formData.value });
    }
    resetForm();
  }
};

// Reset form
const resetForm = () => {
  formData.value = { leaveType: '', icon: 'fa-solid fa-calendar' };
  editingIndex.value = null;
  isEditing.value = false;
};

// Edit leave type
const editLeaveType = (index: number) => {
  formData.value = { ...leaveTypes.value[index] };
  editingIndex.value = index;
  isEditing.value = true;
};

// Delete leave type
const deleteLeaveType = (index: number) => {
  leaveTypes.value.splice(index, 1);
};

const menuItems = ['Defaults', 'Stock Validations', 'Stock Reservation', 'Serial & Batch Item', 'Stock Planning', 'Stock Closing'];

const activeTab = ref(0);
const comments = ref('');
const commentList = ref<string[]>([]);

const settings = ref({
  itemNaming: 'Item Code',
  valuationMethod: 'FIFO',
  defaultWarehouse: '',
  autoInsertPrice: false,
  updatePriceList: false,
  allowNegativeStock: false,
  allowOverDelivery: false,
});

const namingOptions = ['Item Code', 'Item Name', 'Custom'];
const valuationOptions = ['FIFO', 'LIFO', 'Moving Average'];

function addComment() {
  if (comments.value.trim() !== '') {
    commentList.value.push(comments.value);
    comments.value = '';
  }
} 
</script>

<template>
  <Layout>
    <Head title="Leave Type" />
    <PageHeader title="Leave Type" pageTitle="HR" />
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12">
                  <b-tabs v-model="activeTab" pills>
                    <b-tab title="Defaults">
                      <BRow>
                        <BCol xl="4">
                          <b-card no-body>
                            <b-card-header>
                              <h5>Item Defaults</h5>
                            </b-card-header>
                            <b-card-body>
                              <b-form-group label="Item Naming By" label-for="item-naming">
                                <b-form-select id="item-naming" v-model="settings.itemNamingBy" :options="namingOptions" disabled></b-form-select>
                              </b-form-group>
                              <b-form-group label="Default Valuation Method" label-for="valuation-method">
                                <b-form-select id="valuation-method" v-model="settings.valuationMethod" :options="valuationOptions" disabled></b-form-select>
                              </b-form-group>
                              <b-form-group label="Default Warehouse" label-for="default-warehouse">
                                <b-form-input id="default-warehouse" v-model="settings.defaultWarehouse" readonly></b-form-input>
                              </b-form-group>
                              <b-form-group label="Default Stock UOM" label-for="stock-uom">
                                <b-form-input id="stock-uom" v-model="settings.stockUom" readonly></b-form-input>
                              </b-form-group>
                            </b-card-body>
                          </b-card>
                        </BCol>
                        <BCol xl="4">
                          <!-- Price List Defaults Section -->
                          <b-card class="mt-4">
                            <b-card-header>
                              <h5>Price List Defaults</h5>
                            </b-card-header>
                            <b-card-body>
                              <div class="row">
                                <div class="col-sm-6">
                                  <b-form-checkbox v-model="settings.autoInsertPrice" disabled>
                                    Auto Insert Item Price If Missing
                                  </b-form-checkbox>
                                </div>
                                <div class="col-sm-6">
                                  <b-form-checkbox v-model="settings.updatePriceList" disabled>
                                    Update Existing Price List Rate
                                  </b-form-checkbox>
                                </div>
                              </div>
                            </b-card-body>
                          </b-card>
                        </BCol>
                        <BCol xl="4">
                          <!-- Stock UOM Quantity Section -->
                          <b-card class="mt-4">
                            <b-card-header>
                              <h5>Stock UOM Quantity</h5>
                            </b-card-header>
                            <b-card-body>
                              <div class="row">
                                <div class="col-sm-6">
                                  <b-form-checkbox v-model="settings.editStockUomSales" disabled>
                                    Allow to Edit Stock UOM Qty for Sales Documents
                                  </b-form-checkbox>
                                </div>
                                <div class="col-sm-6">
                                  <b-form-checkbox v-model="settings.editStockUomPurchase" disabled>
                                    Allow to Edit Stock UOM Qty for Purchase Documents
                                  </b-form-checkbox>
                                </div>
                              </div>
                            </b-card-body>
                          </b-card>
                        </BCol>
                      </BRow>
                    </b-tab>

                    <b-tab title="Stock Validations">
                      <div class="card p-4">
                        <h6>Stock Validations Settings</h6>
                        <b-form-checkbox v-model="settings.allowNegativeStock">Allow Negative Stock</b-form-checkbox>
                        <b-form-checkbox v-model="settings.allowOverDelivery">Allow Over Delivery</b-form-checkbox>
                      </div>
                    </b-tab>

                    <b-tab title="Comments">
                      <div class="card p-4">
                        <b-form-textarea v-model="comments" placeholder="Type a reply / comment"></b-form-textarea>
                        <b-button class="mt-3" @click="addComment">Submit</b-button>
                        <div class="mt-4">
                          <div v-for="(comment, index) in commentList" :key="index" class="comment mb-3">
                            <span>{{ comment }}</span>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                  </b-tabs>
                </BCol>
                <div v-if="submitted" class="confirmation-message">
                  <p>Thank you, {{ formData.employeeName }}! Your request has been submitted.</p>
                </div>
                <form @submit.prevent="submitForm">
                  <div class="mb-3">
                    <label for="leaveType" class="form-label">Leave Type</label>
                    <input id="leaveType" class="form-control" v-model="formData.leaveType" type="text" placeholder="Enter leave type" :class="{ 'is-invalid': errors.leaveType }"/>
                    <span v-if="errors.leaveType" class="error">{{ errors.leaveType }}</span>
                  </div>

                  <div class="mb-3">
                    <label for="leaveDays" class="form-label">Leave Days</label>
                    <input id="leaveDays" class="form-control" v-model="formData.leaveDays" type="text" placeholder="Enter leave days" :class="{ 'is-invalid': errors.leaveDays }"/>
                    <span v-if="errors.leaveDays" class="error">{{ errors.leaveDays }}</span>
                  </div>

                  <div class="mb-3">
                    <label for="icon" class="form-label">Select Icon</label>
                    <!-- Custom Dropdown -->
                    <div class="dropdown" @click="toggleDropdown">
                      <input type="text" class="form-control rounded-end flag-input" readonly placeholder="Select Icon" v-model="selectedIcon.name">
                      <ul v-if="isOpen" class="dropdown-options">
                        <li v-for="icon in icons" :key="icon.name" @click="selectIcon(icon)" class="dropdown-item d-flex cursor-pointer m-2">
                          <i :class="'mdi mdi-' + icon.name"></i> {{ icon.name }}
                        </li>
                      </ul>
                    </div>
                  </div>

                  <!-- Submit Button -->
                  <BCol lg="12">
                    <div class="hstack gap-2 justify-content-end">
                      <BButton type="submit" variant="primary">{{ isEditing ? 'Update' : 'Add' }} Leave Type</BButton>
                    </div>
                  </BCol>
                </form>
              </BCardBody>
            </BCard>
          </BCol>
          <BCol xl="8">
            <BCard no-body>
              <BCardHeader class="border-0">
                <BCardTitle class="mb-0">Leave Types</BCardTitle>
              </BCardHeader>
              <BCardBody class="pt-0">
                <!-- Display Table -->
                <table class="table table-bordered table-striped mt-3">
                  <thead>
                    <tr>
                      <th>Icon</th>
                      <th>Leave Type</th>
                      <th>Leave Days</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(leave, index) in leaveTypes" :key="index">
                      <td><i :class="leave.icon"></i></td>
                      <td>{{ leave.leaveType }}</td>
                      <td>{{ leave.leaveDays }}</td>
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