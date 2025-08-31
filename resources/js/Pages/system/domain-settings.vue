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
    <Head title="Domain Settings" />
    <PageHeader title="Domain Settings" pageTitle="Settings" />
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12" class="mt-3">
                  <div class="form-message hidden"></div>
                  <div class="std-form-layout">
                    <div class="form-layout">
                      <div class="form-message hidden"></div>
                      <div class="form-page">
                        <div class="form-dashboard">
                          <div class="row form-dashboard-section progress-area hide-control" data-fieldname="undefined">
                            <div class="section-body"></div>
                          </div>
                          <div class="row form-dashboard-section form-heatmap hide-control" data-fieldname="undefined">
                            <div class="section-head collapsible">
                              Activity
                              <span class="ml-2 collapse-indicator mb-1" style="">
                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                  <use class="mb-1" href="#es-line-up"></use>
                                </svg>
                              </span>
                            </div>
                            <div class="section-body">
                              <div id="heatmap-domain_settings" class="heatmap"></div>
                              <div class="text-muted small heatmap-message hidden"></div>
                            </div>
                          </div>
                          <div class="row form-dashboard-section form-graph hide-control" data-fieldname="undefined">
                            <div class="section-head collapsible">
                              Graph
                              <span class="ml-2 collapse-indicator mb-1" style="">
                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                  <use class="mb-1" href="#es-line-up"></use>
                                </svg>
                              </span>
                            </div>
                            <div class="section-body"></div>
                          </div>
                          <div class="row form-dashboard-section form-stats hide-control" data-fieldname="undefined">
                            <div class="section-head collapsible">
                              Stats
                              <span class="ml-2 collapse-indicator mb-1" style="">
                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                  <use class="mb-1" href="#es-line-up"></use>
                                </svg>
                              </span>
                            </div>
                            <div class="section-body">
                              <div class="row"></div>
                            </div>
                          </div>
                          <div class="row form-dashboard-section form-links hide-control" data-fieldname="undefined">
                            <div class="section-head collapsible">
                              Connections
                              <span class="ml-2 collapse-indicator mb-1" style="">
                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                  <use class="mb-1" href="#es-line-up"></use>
                                </svg>
                              </span>
                            </div>
                            <div class="section-body">
                              <div class="transactions"></div>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section empty-section" data-fieldname="__section_1">
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_1">
                              <form>
                                <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="__newname">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;"></label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">__newname</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="active_domains_sb">
                          <div class="section-head">
                            Active Domains
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_2">
                              <form>
                                <div class="frappe-control" data-fieldtype="HTML" data-fieldname="domains_html">
                                  <span class="tooltip-content">domains_html</span>
                                  <div class="frappe-control" data-fieldtype="MultiCheck" data-fieldname="domains_multicheck">
                                    <span class="tooltip-content">domains_multicheck</span>
                                    <div class="bulk-select-options" style="display: none;">
                                      <button class="btn btn-xs btn-default select-all">
                                      Select All
                                      </button>
                                      <button class="btn btn-xs btn-default deselect-all">
                                      Unselect All
                                      </button>
                                    </div>
                                    <div class="load-state text-muted small" style="display: none;">Loading...</div>
                                    <div class="checkbox-options" style="--checkbox-options-columns: undefined;">
                                      <div class="checkbox unit-checkbox">
                                        <label title="">
                                        <input type="checkbox" data-unit="Manufacturing">
                                        <span class="label-area" data-unit="Manufacturing">Manufacturing</span>
                                        </label>
                                      </div>
                                      <div class="checkbox unit-checkbox">
                                        <label title="">
                                        <input type="checkbox" data-unit="Retail">
                                        <span class="label-area" data-unit="Retail">Retail</span>
                                        </label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="frappe-control hide-control form-group" data-fieldtype="Table" data-fieldname="active_domains">
                                  <span class="tooltip-content">active_domains</span>
                                  <div class="grid-field">
                                    <label class="control-label">Active Domains</label>
                                    <span class="help"></span>
                                    <p class="text-muted small grid-description" style="display: none;"></p>
                                    <div class="grid-custom-buttons"></div>
                                    <div class="form-grid-container">
                                      <div class="form-grid">
                                        <div class="grid-heading-row"></div>
                                        <div class="grid-body">
                                          <div class="rows"></div>
                                          <div class="grid-empty text-center">
                                            <img src="/assets/frappe/images/ui-states/grid-empty-state.svg" alt="Grid Empty State" class="grid-empty-illustration">
                                            No Data
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="small form-clickable-section grid-footer">
                                      <div class="flex justify-between">
                                        <div class="grid-buttons">
                                          <button type="button" class="btn btn-xs btn-danger grid-remove-rows hidden" data-action="delete_rows">
                                          Delete
                                          </button>
                                          <button type="button" class="btn btn-xs btn-danger grid-remove-all-rows hidden" data-action="delete_all_rows">
                                          Delete All
                                          </button>
                                          <!-- hack to allow firefox include this in tabs -->
                                          <button type="button" class="btn btn-xs btn-secondary grid-add-row">
                                          Add Row
                                          </button>
                                          <button type="button" class="grid-add-multiple-rows btn btn-xs btn-secondary hidden">
                                          Add Multiple
                                          </button>
                                        </div>
                                        <div class="grid-pagination"></div>
                                        <div class="grid-bulk-actions text-right">
                                          <button type="button" class="grid-download btn btn-xs btn-secondary hidden">
                                          Download
                                          </button>
                                          <button type="button" class="grid-upload btn btn-xs btn-secondary hidden">
                                          Upload
                                          </button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </BCol>
                <!-- <div v-if="submitted" class="confirmation-message">
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
                    <div class="dropdown" @click="toggleDropdown">
                      <input type="text" class="form-control rounded-end flag-input" readonly placeholder="Select Icon" v-model="selectedIcon.name">
                      <ul v-if="isOpen" class="dropdown-options">
                        <li v-for="icon in icons" :key="icon.name" @click="selectIcon(icon)" class="dropdown-item d-flex cursor-pointer m-2">
                          <i :class="'mdi mdi-' + icon.name"></i> {{ icon.name }}
                        </li>
                      </ul>
                    </div>
                  </div>

                  <BCol lg="12">
                    <div class="hstack gap-2 justify-content-end">
                      <BButton type="submit" variant="primary">{{ isEditing ? 'Update' : 'Add' }} Leave Type</BButton>
                    </div>
                  </BCol>
                </form> -->
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