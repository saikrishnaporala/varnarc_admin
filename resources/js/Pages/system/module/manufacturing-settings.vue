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
    <Head title="Manufacturing Settings" />
    <PageHeader title="Manufacturing Settings" pageTitle="Settings" subTitle="Module"/>
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12" class="mt-3">
                  <div class="form-message hidden"></div>
                  <b-tabs v-model="activeTab" pills>
                    <b-tab title="BOM and Production">
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
                            <div id="heatmap-manufacturing_settings" class="heatmap"></div>
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
                      <div class="row form-section card-section empty-section" data-fieldname="__section_2">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="raw_materials_consumption_section">
                        <div class="section-head">
                          Raw Materials Consumption 
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_3">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="material_consumption">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="material_consumption" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Continuous Material Consumption</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Allow material consumptions without immediately manufacturing finished goods against a Work Order</p>
                                </div>
                                <span class="tooltip-content">material_consumption</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="get_rm_cost_from_consumption_entry">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="get_rm_cost_from_consumption_entry" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Get Raw Materials Cost from Consumption Entry</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">get_rm_cost_from_consumption_entry</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_3">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="backflush_raw_materials_based_on">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Backflush Raw Materials Based On</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="BOM">BOM</option>
                                        <option value="Material Transferred for Manufacture">Material Transferred for Manufacture</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">BOM</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">backflush_raw_materials_based_on</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="validate_components_quantities_per_bom">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="validate_components_quantities_per_bom" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Validate Components Quantities Per BOM</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">validate_components_quantities_per_bom</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="bom_section">
                        <div class="section-head">
                          BOM
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_4">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="update_bom_costs_automatically">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="update_bom_costs_automatically" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Update BOM Cost Automatically</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Update BOM cost automatically via scheduler, based on the latest Valuation Rate/Price List Rate/Last Purchase Rate of raw materials</p>
                                </div>
                                <span class="tooltip-content">update_bom_costs_automatically</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_lhyt">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="manufacture_sub_assembly_in_operation">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="manufacture_sub_assembly_in_operation" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Manufacture Sub-assembly in Operation</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If enabled then system will manufacture Sub-assembly against the Job Card (operation).</p>
                                </div>
                                <span class="tooltip-content">manufacture_sub_assembly_in_operation</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="section_break_6">
                        <div class="section-head">
                          Default Warehouses for Production
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_5">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="default_wip_warehouse">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Work In Progress Warehouse</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Warehouse" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
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
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">default_wip_warehouse</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="default_fg_warehouse">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Finished Goods Warehouse</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Warehouse" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
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
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">default_fg_warehouse</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_11">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="default_scrap_warehouse">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Scrap Warehouse</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Warehouse" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_4" role="combobox">
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
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">default_scrap_warehouse</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="over_production_for_sales_and_work_order_section">
                        <div class="section-head">
                          Overproduction for Sales and Work Order
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_6">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Percent" data-fieldname="overproduction_percentage_for_sales_order">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Overproduction Percentage For Sales Order</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Percent" data-fieldname="overproduction_percentage_for_sales_order" placeholder="" data-doctype="Manufacturing Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0%</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">overproduction_percentage_for_sales_order</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_16">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Percent" data-fieldname="overproduction_percentage_for_work_order">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Overproduction Percentage For Work Order</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Percent" data-fieldname="overproduction_percentage_for_work_order" placeholder="" data-doctype="Manufacturing Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0%</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">overproduction_percentage_for_work_order</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Job Card and Capacity Planning">
                      <div class="row form-section card-section visible-section" data-fieldname="__section_7">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_7">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="add_corrective_operation_cost_in_finished_good_valuation">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="add_corrective_operation_cost_in_finished_good_valuation" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Add Corrective Operation Cost in Finished Good Valuation</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">add_corrective_operation_cost_in_finished_good_valuation</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_24">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="job_card_excess_transfer">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="job_card_excess_transfer" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Excess Material Transfer</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Allow transferring raw materials even after the Required Quantity is fulfilled</p>
                                </div>
                                <span class="tooltip-content">job_card_excess_transfer</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="capacity_planning">
                        <div class="section-head">
                          Capacity Planning
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_8">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_capacity_planning">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_capacity_planning" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Disable Capacity Planning</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">disable_capacity_planning</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_overtime">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_overtime" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Overtime</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Plan time logs outside Workstation working hours</p>
                                </div>
                                <span class="tooltip-content">allow_overtime</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_production_on_holidays">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_production_on_holidays" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Production on Holidays</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_production_on_holidays</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_5">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="capacity_planning_for_days">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Capacity Planning For (Days)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="capacity_planning_for_days" placeholder="" data-doctype="Manufacturing Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">30</div>
                                    <p class="help-box small text-muted">Plan operations X days in advance</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">capacity_planning_for_days</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="mins_between_operations">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Time Between Operations (Mins)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="mins_between_operations" placeholder="" data-doctype="Manufacturing Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">Default: 10 mins</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">mins_between_operations</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="other_settings_section">
                        <div class="section-head">
                          Other Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_9">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="set_op_cost_and_scrape_from_sub_assemblies">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="set_op_cost_and_scrape_from_sub_assemblies" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Set Operating Cost / Scrape Items From Sub-assemblies</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">In the case of 'Use Multi-Level BOM' in a work order, if the user wishes to add sub-assembly costs to Finished Goods items without using a job card as well the scrap items, then this option needs to be enable.</p>
                                </div>
                                <span class="tooltip-content">set_op_cost_and_scrape_from_sub_assemblies</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_23">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="make_serial_no_batch_from_work_order">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="make_serial_no_batch_from_work_order" placeholder="" data-doctype="Manufacturing Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Make Serial No / Batch from Work Order</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">System will automatically create the serial numbers / batch for the Finished Good on submission of work order</p>
                                </div>
                                <span class="tooltip-content">make_serial_no_batch_from_work_order</span>
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