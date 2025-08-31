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
    <Head title="System Settings" />
    <PageHeader title="System Settings" pageTitle="Settings" />
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12" class="mt-3">
                  <div class="form-message hidden"></div>
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
                              <div id="heatmap-system_settings" class="heatmap"></div>
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
                        <div class="row form-section card-section visible-section" data-fieldname="localization">
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_2">
                              <form>
                                <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="app_name">
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
                                  <span class="tooltip-content">app_name</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="country">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Country</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="link-field ui-front" style="position: relative;">
                                          <div class="awesomplete">
                                            <input type="text" class="input-with-feedback form-control" data-target="Country" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
                                            <ul hidden="" role="listbox" id="awesomplete_list_2"></ul>
                                            <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                          </div>
                                          <span class="link-btn">
                                            <a class="btn-clear no-decoration">
                                              <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#icon-close-alt"></use>
                                              </svg>
                                            </a>
                                            <a class="btn-open no-decoration" title="Open Link">
                                              <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#icon-arrow-right"></use>
                                              </svg>
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;"><a href="/app/country/India" data-doctype="Country" data-name="India" data-value="India">India</a></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">country</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="language">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label reqd" style="padding-right: 0px;">Language</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="link-field ui-front" style="position: relative;">
                                          <div class="awesomplete">
                                            <input type="text" class="input-with-feedback form-control bold" data-target="Language" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
                                            <ul hidden="" role="listbox" id="awesomplete_list_3"></ul>
                                            <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                          </div>
                                          <span class="link-btn">
                                            <a class="btn-clear no-decoration">
                                              <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#icon-close-alt"></use>
                                              </svg>
                                            </a>
                                            <a class="btn-open no-decoration" title="Open Link">
                                              <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#icon-arrow-right"></use>
                                              </svg>
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input bold" style="display: none;"><a href="/app/language/en" data-doctype="Language" data-name="en" data-value="en">English</a></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">language</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="column_break_3">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="time_zone">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label reqd" style="padding-right: 0px;">Time Zone</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input" style="display: none;"></div>
                                      <div class="control-value like-disabled-input bold" style="">Asia/Kolkata</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">time_zone</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="currency">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Currency</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="link-field ui-front" style="position: relative;">
                                          <div class="awesomplete">
                                            <input type="text" class="input-with-feedback form-control" data-target="Currency" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_4" role="combobox">
                                            <ul hidden="" role="listbox" id="awesomplete_list_4"></ul>
                                            <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                          </div>
                                          <span class="link-btn">
                                            <a class="btn-clear no-decoration">
                                              <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#icon-close-alt"></use>
                                              </svg>
                                            </a>
                                            <a class="btn-open no-decoration" title="Open Link">
                                              <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#icon-arrow-right"></use>
                                              </svg>
                                            </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;"><a href="/app/currency/INR" data-doctype="Currency" data-name="INR" data-value="INR">INR</a></div>
                                      <p class="help-box small text-muted">Default display currency</p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">currency</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_onboarding">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_onboarding" placeholder="" data-doctype="System Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                    <span class="label-area">Enable Onboarding</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">enable_onboarding</span>
                                </div>
                                <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="setup_complete">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"></span>
                                    <span class="disp-area"></span>
                                    <span class="label-area"></span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">setup_complete</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_document_sharing">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_document_sharing" placeholder="" data-doctype="System Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Disable Document Sharing</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">disable_document_sharing</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="date_and_number_format">
                          <div class="section-head">
                            Date and Number Format
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_3">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="date_format">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label reqd" style="padding-right: 0px;">Date Format</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input flex align-center">
                                        <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold">
                                          <option value="yyyy-mm-dd">yyyy-mm-dd</option>
                                          <option value="dd-mm-yyyy">dd-mm-yyyy</option>
                                          <option value="dd/mm/yyyy">dd/mm/yyyy</option>
                                          <option value="dd.mm.yyyy">dd.mm.yyyy</option>
                                          <option value="mm/dd/yyyy">mm/dd/yyyy</option>
                                          <option value="mm-dd-yyyy">mm-dd-yyyy</option>
                                        </select>
                                        <div class="select-icon ">
                                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#icon-select"></use>
                                          </svg>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input bold" style="display: none;">dd-mm-yyyy</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">date_format</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="time_format">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label reqd" style="padding-right: 0px;">Time Format</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input flex align-center">
                                        <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold">
                                          <option value="HH:mm:ss">HH:mm:ss</option>
                                          <option value="HH:mm">HH:mm</option>
                                        </select>
                                        <div class="select-icon ">
                                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#icon-select"></use>
                                          </svg>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input bold" style="display: none;">HH:mm:ss</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">time_format</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="number_format">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label reqd" style="padding-right: 0px;">Number Format</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input flex align-center">
                                        <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold">
                                          <option value="#,###.##">#,###.##</option>
                                          <option value="#.###,##">#.###,##</option>
                                          <option value="# ###.##"># ###.##</option>
                                          <option value="# ###,##"># ###,##</option>
                                          <option value="#'###.##">#'###.##</option>
                                          <option value="#, ###.##">#, ###.##</option>
                                          <option value="#,##,###.##">#,##,###.##</option>
                                          <option value="#,###.###">#,###.###</option>
                                          <option value="#.###">#.###</option>
                                          <option value="#,###">#,###</option>
                                        </select>
                                        <div class="select-icon ">
                                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#icon-select"></use>
                                          </svg>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input bold" style="display: none;">#,##,###.##</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">number_format</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="use_number_format_from_currency">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="use_number_format_from_currency" placeholder="" data-doctype="System Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Use Number Format from Currency</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">use_number_format_from_currency</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="first_day_of_the_week">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">First Day of the Week</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input flex align-center">
                                        <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                          <option value="Sunday">Sunday</option>
                                          <option value="Monday">Monday</option>
                                          <option value="Tuesday">Tuesday</option>
                                          <option value="Wednesday">Wednesday</option>
                                          <option value="Thursday">Thursday</option>
                                          <option value="Friday">Friday</option>
                                          <option value="Saturday">Saturday</option>
                                        </select>
                                        <div class="select-icon ">
                                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#icon-select"></use>
                                          </svg>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;">Sunday</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">first_day_of_the_week</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="column_break_7">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="float_precision">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Float Precision</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input flex align-center">
                                        <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                          <option></option>
                                          <option value="2">2</option>
                                          <option value="3">3</option>
                                          <option value="4">4</option>
                                          <option value="5">5</option>
                                          <option value="6">6</option>
                                          <option value="7">7</option>
                                          <option value="8">8</option>
                                          <option value="9">9</option>
                                        </select>
                                        <div class="select-icon ">
                                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#icon-select"></use>
                                          </svg>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;">3</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">float_precision</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="currency_precision">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Currency Precision</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input flex align-center">
                                        <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                          <option></option>
                                          <option value="0">0</option>
                                          <option value="1">1</option>
                                          <option value="2">2</option>
                                          <option value="3">3</option>
                                          <option value="4">4</option>
                                          <option value="5">5</option>
                                          <option value="6">6</option>
                                          <option value="7">7</option>
                                          <option value="8">8</option>
                                          <option value="9">9</option>
                                        </select>
                                        <div class="select-icon ">
                                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#icon-select"></use>
                                          </svg>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted">If not set, the currency precision will depend on number format</p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">currency_precision</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="rounding_method">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Rounding Method</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input flex align-center">
                                        <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                          <option value="Banker's Rounding">Banker's Rounding</option>
                                          <option value="Commercial Rounding">Commercial Rounding</option>
                                        </select>
                                        <div class="select-icon ">
                                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#icon-select"></use>
                                          </svg>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;">Banker's Rounding</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">rounding_method</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="permissions">
                          <div class="section-head">
                            Permissions
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_4">
                              <form>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="apply_strict_user_permissions">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="apply_strict_user_permissions" placeholder="" data-doctype="System Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Apply Strict User Permissions</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted">If Apply Strict User Permission is checked and User Permission is defined for a DocType for a User, then all the documents where value of the link is blank, will not be shown to that User</p>
                                  </div>
                                  <span class="tooltip-content">apply_strict_user_permissions</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="column_break_21">
                              <form>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_older_web_view_links">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_older_web_view_links" placeholder="" data-doctype="System Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Allow Older Web View Links (Insecure)</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">allow_older_web_view_links</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </BRow>
                    </b-tab>
                    <b-tab title="Login">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_5">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="security">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_6">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="session_expiry">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Session Expiry (idle timeout)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">170:00</div>
                                    <p class="help-box small text-muted">Example: Setting this to 24:00 will log out a user if they are not active for 24:00 hours.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">session_expiry</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="document_share_key_expiry">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Document Share Key Expiry (in Days)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="document_share_key_expiry" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">30</div>
                                    <p class="help-box small text-muted">Number of days after which the document Web View link shared on email will be expired</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">document_share_key_expiry</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_txqh">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="deny_multiple_sessions">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="deny_multiple_sessions" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow only one session per user</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Note: Multiple sessions will be allowed in case of mobile device</p>
                                </div>
                                <span class="tooltip-content">deny_multiple_sessions</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_user_pass_login">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_user_pass_login" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Disable Username/Password Login</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Make sure to configure a Social Login Key before disabling to prevent lockout</p>
                                </div>
                                <span class="tooltip-content">disable_user_pass_login</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="login_methods_section">
                        <div class="section-head">
                          Login Methods
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_7">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_login_using_mobile_number">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_login_using_mobile_number" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Login using Mobile Number</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">User can login using Email id or Mobile number</p>
                                </div>
                                <span class="tooltip-content">allow_login_using_mobile_number</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_login_using_user_name">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_login_using_user_name" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Login using User Name</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">User can login using Email id or User Name</p>
                                </div>
                                <span class="tooltip-content">allow_login_using_user_name</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_uhqk">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="login_with_email_link">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="login_with_email_link" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Login with email link</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Allow users to log in without a password, using a login link sent to their email</p>
                                </div>
                                <span class="tooltip-content">login_with_email_link</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="login_with_email_link_expiry">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Login with email link expiry (in minutes)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="login_with_email_link_expiry" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">10</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">login_with_email_link_expiry</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="rate_limit_email_link_login">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Rate limit for email link login</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="rate_limit_email_link_login" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">You can set a high value here if multiple users will be logging in from the same network.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">rate_limit_email_link_login</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="brute_force_security">
                        <div class="section-head">
                          Brute Force Security
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_8">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="allow_consecutive_login_attempts">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Allow Consecutive Login Attempts </label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="allow_consecutive_login_attempts" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">10</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">allow_consecutive_login_attempts</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_34">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="allow_login_after_fail">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Allow Login After Fail</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="allow_login_after_fail" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">60</div>
                                    <p class="help-box small text-muted">In seconds</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">allow_login_after_fail</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="two_factor_authentication">
                        <div class="section-head">
                          Two Factor Authentication
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_9">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_two_factor_auth">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_two_factor_auth" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Enable Two Factor Auth</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">enable_two_factor_auth</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="bypass_2fa_for_retricted_ip_users">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="bypass_2fa_for_retricted_ip_users" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Bypass Two Factor Auth for users who login from restricted IP Address</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If enabled, users who login from Restricted IP Address, won't be prompted for Two Factor Auth</p>
                                </div>
                                <span class="tooltip-content">bypass_2fa_for_retricted_ip_users</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="bypass_restrict_ip_check_if_2fa_enabled">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="bypass_restrict_ip_check_if_2fa_enabled" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Bypass restricted IP Address check If Two Factor Auth Enabled</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If enabled, all users can login from any IP Address using Two Factor Auth. This can also be set only for specific user(s) in User Page</p>
                                </div>
                                <span class="tooltip-content">bypass_restrict_ip_check_if_2fa_enabled</span>
                              </div>
                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Select" data-fieldname="two_factor_method">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Two Factor Authentication method</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="OTP App">OTP App</option>
                                        <option value="SMS">SMS</option>
                                        <option value="Email">Email</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">OTP App</div>
                                    <p class="help-box small text-muted">Choose authentication method to be used by all users</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">two_factor_method</span>
                              </div>
                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Int" data-fieldname="lifespan_qrcode_image">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Expiry time of QR Code Image Page</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="lifespan_qrcode_image" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">Time in seconds to retain QR code image on server. Min:<strong>240</strong></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">lifespan_qrcode_image</span>
                              </div>
                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="otp_issuer_name">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">OTP Issuer Name</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">Frappe Framework</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">otp_issuer_name</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Password">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_10">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="password_settings">
                        <div class="section-head">
                          Password
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_11">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="logout_on_password_reset">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="logout_on_password_reset" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Logout All Sessions on Password Reset</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">logout_on_password_reset</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="force_user_to_reset_password">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Force User to Reset Password</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="force_user_to_reset_password" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">In Days</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">force_user_to_reset_password</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Duration" data-fieldname="reset_password_link_expiry_duration">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Reset Password Link Expiry Duration</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Duration" data-fieldname="reset_password_link_expiry_duration" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">20m</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">reset_password_link_expiry_duration</span>
                                <div class="duration-picker" style="display: none;">
                                  <div class="picker-row row">
                                    <div class="col duration-col">
                                      <div class="row duration-row"><input class="input-sm duration-input" data-duration="days" type="number" min="0" value="0"></div>
                                      <div class="row duration-row duration-label">days</div>
                                    </div>
                                    <div class="col duration-col">
                                      <div class="row duration-row"><input class="input-sm duration-input" data-duration="hours" type="number" min="0" value="0"></div>
                                      <div class="row duration-row duration-label">hours</div>
                                    </div>
                                    <div class="col duration-col">
                                      <div class="row duration-row"><input class="input-sm duration-input" data-duration="minutes" type="number" min="0" value="0"></div>
                                      <div class="row duration-row duration-label">minutes</div>
                                    </div>
                                    <div class="col duration-col">
                                      <div class="row duration-row"><input class="input-sm duration-input" data-duration="seconds" type="number" min="0" value="0"></div>
                                      <div class="row duration-row duration-label">seconds</div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="password_reset_limit">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Password Reset Link Generation Limit</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="password_reset_limit" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">3</div>
                                    <p class="help-box small text-muted">Hourly rate limit for generating password reset links</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">password_reset_limit</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_31">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_password_policy">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_password_policy" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Enable Password Policy</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If enabled, the password strength will be enforced based on the Minimum Password Score value. A value of 2 being medium strong and 4 being very strong.</p>
                                </div>
                                <span class="tooltip-content">enable_password_policy</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="minimum_password_score">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Minimum Password Score</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">2</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">minimum_password_score</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Email">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_12">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="email">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_13">
                            <form>
                              <div class="frappe-control" data-fieldtype="Small Text" data-fieldname="email_footer_address">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Email Footer Address</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Small Text" data-fieldname="email_footer_address" placeholder="" data-doctype="System Settings" style="height: 150px;"></textarea></div>
                                    <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                    <p class="help-box small text-muted">Your organization name and address for the email footer.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">email_footer_address</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="email_retry_limit">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Email Retry Limit</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="email_retry_limit" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">3</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">email_retry_limit</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_18">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_standard_email_footer">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_standard_email_footer" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Disable Standard Email Footer</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">disable_standard_email_footer</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="hide_footer_in_auto_email_reports">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="hide_footer_in_auto_email_reports" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Hide footer in auto email reports</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">hide_footer_in_auto_email_reports</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="attach_view_link">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="attach_view_link" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Include Web View Link in Email</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">attach_view_link</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="store_attached_pdf_document">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="store_attached_pdf_document" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Store Attached PDF Document</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">When sending document using email, store the PDF on Communication. Warning: This can increase your storage usage.</p>
                                </div>
                                <span class="tooltip-content">store_attached_pdf_document</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="welcome_email_template">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Welcome Email Template</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Email Template" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_5" role="combobox">
                                          <ul hidden="" role="listbox" id="awesomplete_list_5"></ul>
                                          <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                        </div>
                                        <span class="link-btn">
                                          <a class="btn-clear no-decoration">
                                            <svg class="icon  icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#icon-close-alt"></use>
                                            </svg>
                                          </a>
                                          <a class="btn-open no-decoration" title="Open Link">
                                            <svg class="icon  icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#icon-arrow-right"></use>
                                            </svg>
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">welcome_email_template</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="reset_password_template">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Reset Password Template</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Email Template" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_6" role="combobox">
                                          <ul hidden="" role="listbox" id="awesomplete_list_6"></ul>
                                          <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                        </div>
                                        <span class="link-btn">
                                          <a class="btn-clear no-decoration">
                                            <svg class="icon  icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#icon-close-alt"></use>
                                            </svg>
                                          </a>
                                          <a class="btn-open no-decoration" title="Open Link">
                                            <svg class="icon  icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#icon-arrow-right"></use>
                                            </svg>
                                          </a>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">reset_password_template</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Files">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_14">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="files_section">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_15">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="max_file_size">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Max File Size (MB)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="max_file_size" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">max_file_size</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_guests_to_upload_files">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_guests_to_upload_files" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Guests to Upload Files</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">When enabled this will allow guests to upload files to your application, You can enable this if you wish to collect files from user without having them to log in, for example in job applications web form.</p>
                                </div>
                                <span class="tooltip-content">allow_guests_to_upload_files</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="force_web_capture_mode_for_uploads">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="force_web_capture_mode_for_uploads" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Force Web Capture Mode for Uploads</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">When uploading files, force the use of the web-based image capture. If this is unchecked, the default behavior is to use the mobile native camera when use from a mobile is detected.</p>
                                </div>
                                <span class="tooltip-content">force_web_capture_mode_for_uploads</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="strip_exif_metadata_from_uploaded_images">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="strip_exif_metadata_from_uploaded_images" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Strip EXIF tags from uploaded images</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">strip_exif_metadata_from_uploaded_images</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_uqma">
                            <form>
                              <div class="frappe-control" data-fieldtype="Small Text" data-fieldname="allowed_file_extensions">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Allowed File Extensions</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Small Text" data-fieldname="allowed_file_extensions" placeholder="" data-doctype="System Settings" style="height: 150px;"></textarea></div>
                                    <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                    <p class="help-box small text-muted">Provide a list of allowed file extensions for file uploads. Each line should contain one allowed file type. If unset, all file extensions are allowed. Example: <br>CSV<br>JPG<br>PNG</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">allowed_file_extensions</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="App">
                      <div class="row form-section card-section visible-section" data-fieldname="__section_16">
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_16">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="default_app">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default App</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option></option>
                                        <option value="erpnext">erpnext</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted">Redirect to the selected app after login</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">default_app</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Updates">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_17">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="system_updates_section">
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_18">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_system_update_notification">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_system_update_notification" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Disable System Update Notification</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">disable_system_update_notification</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_change_log_notification">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_change_log_notification" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Disable Change Log Notification</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">disable_change_log_notification</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Backups">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_19">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="sec_backup_limit">
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_20">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="backup_limit">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Number of Backups</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="backup_limit" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">3</div>
                                    <p class="help-box small text-muted">Older backups will be automatically deleted</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">backup_limit</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="encrypt_backup">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="encrypt_backup" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Encrypt Backups</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">encrypt_backup</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Advanced">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_21">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="prepared_report_section">
                        <div class="section-head">
                          Reports
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_22">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="max_auto_email_report_per_user">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Max auto email report per user</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="max_auto_email_report_per_user" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">20</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">max_auto_email_report_per_user</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="background_workers">
                        <div class="section-head">
                          Background Workers
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_23">
                            <form>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="enable_scheduler">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"></span>
                                  <span class="disp-area"></span>
                                  <span class="label-area"></span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">enable_scheduler</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="dormant_days">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Run Jobs only Daily if Inactive For (Days)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="dormant_days" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">4</div>
                                    <p class="help-box small text-muted">Will run scheduled jobs only once a day for inactive sites. Default 4 days if set to 0.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">dormant_days</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="telemetry_section">
                        <div class="section-head">
                          Telemetry
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_24">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_error_traceback">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_error_traceback" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Show Full Error and Allow Reporting of Issues to the Developer</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_error_traceback</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_telemetry">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_telemetry" placeholder="" data-doctype="System Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Sending Usage Data for Improving Applications</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">enable_telemetry</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="search_section">
                        <div class="section-head">
                          Search
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_25">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="link_field_results_limit">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Link Field Results Limit</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="link_field_results_limit" placeholder="" data-doctype="System Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">10</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">link_field_results_limit</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                  </b-tabs>
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