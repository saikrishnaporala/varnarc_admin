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
    <Head title="Buying Settings" />
    <PageHeader title="Buying Settings" pageTitle="Settings" subTitle="Module"/>
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12" class="mt-3">
                  <div class="form-message hidden"></div>
                  <b-tabs v-model="activeTab" pills>
                    <b-tab title="Naming Series and Price Defaults">
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
                            <div id="heatmap-buying_settings" class="heatmap"></div>
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
                      <div class="row form-section card-section visible-section" data-fieldname="__section_2">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_2">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="supp_master_name">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Supplier Naming By</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="Supplier Name">Supplier Name</option>
                                        <option value="Naming Series">Naming Series</option>
                                        <option value="Auto Name">Auto Name</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">Supplier Name</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">supp_master_name</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="supplier_group">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Supplier Group</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Supplier Group" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
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
                                <span class="tooltip-content">supplier_group</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="buying_price_list">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Buying Price List</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Price List" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
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
                                    <div class="control-value like-disabled-input" style="display: none;"><a href="/app/price-list/Standard%20Buying" data-doctype="Price List" data-name="Standard Buying" data-value="Standard Buying">Standard Buying</a></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">buying_price_list</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_4">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="maintain_same_rate_action">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label reqd" style="padding-right: 0px;">Action If Same Rate is Not Maintained</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold">
                                        <option value="Stop">Stop</option>
                                        <option value="Warn">Warn</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input bold" style="display: none;">Stop</div>
                                    <p class="help-box small text-muted">Configure the action to stop the transaction or just warn if the same rate is not maintained.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">maintain_same_rate_action</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="role_to_override_stop_action">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Role Allowed to Override Stop Action</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Role" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_4" role="combobox">
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
                                <span class="tooltip-content">role_to_override_stop_action</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Transaction Settings">
                      <div class="row form-section card-section visible-section" data-fieldname="__section_3">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_3">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="po_required">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Is Purchase Order Required for Purchase Invoice &amp; Receipt Creation?</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">No</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">po_required</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="pr_required">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Is Purchase Receipt Required for Purchase Invoice Creation?</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">No</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">pr_required</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Float" data-fieldname="blanket_order_allowance">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Blanket Order Allowance (%)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Float" data-fieldname="blanket_order_allowance" placeholder="" data-doctype="Buying Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">Percentage you are allowed to order beyond the Blanket Order quantity.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">blanket_order_allowance</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="project_update_frequency">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Update frequency of Project</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="Each Transaction">Each Transaction</option>
                                        <option value="Manual">Manual</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">Each Transaction</div>
                                    <p class="help-box small text-muted">How often should Project be updated of Total Purchase Cost ?</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">project_update_frequency</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_12">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="maintain_same_rate">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="maintain_same_rate" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Maintain Same Rate Throughout the Purchase Cycle</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">maintain_same_rate</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="set_landed_cost_based_on_purchase_invoice_rate">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="set_landed_cost_based_on_purchase_invoice_rate" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Set Landed Cost Based on Purchase Invoice Rate</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Users can enable the checkbox If they want to adjust the incoming rate (set using purchase receipt) based on the purchase invoice rate.</p>
                                </div>
                                <span class="tooltip-content">set_landed_cost_based_on_purchase_invoice_rate</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_multiple_items">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_multiple_items" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Allow Item To Be Added Multiple Times in a Transaction</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_multiple_items</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="bill_for_rejected_quantity_in_purchase_invoice">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="bill_for_rejected_quantity_in_purchase_invoice" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Bill for Rejected Quantity in Purchase Invoice</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If checked, Rejected Quantity will be included while making Purchase Invoice from Purchase Receipt.</p>
                                </div>
                                <span class="tooltip-content">bill_for_rejected_quantity_in_purchase_invoice</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_last_purchase_rate">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_last_purchase_rate" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Disable Last Purchase Rate</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">disable_last_purchase_rate</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="show_pay_button">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_pay_button" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Show Pay Button in Purchase Order Portal</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">show_pay_button</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="use_transaction_date_exchange_rate">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="use_transaction_date_exchange_rate" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Use Transaction Date Exchange Rate</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">While making Purchase Invoice from Purchase Order, use Exchange Rate on Invoice's transaction date rather than inheriting it from Purchase Order. Only applies for Purchase Invoice.</p>
                                </div>
                                <span class="tooltip-content">use_transaction_date_exchange_rate</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Subcontracting Settings">
                      <div class="row form-section card-section visible-section" data-fieldname="__section_4">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_4">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="backflush_raw_materials_of_subcontract_based_on">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Backflush Raw Materials of Subcontract Based On</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="BOM">BOM</option>
                                        <option value="Material Transferred for Subcontract">Material Transferred for Subcontract</option>
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
                                <span class="tooltip-content">backflush_raw_materials_of_subcontract_based_on</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_11">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Float" data-fieldname="over_transfer_allowance">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Over Transfer Allowance (%)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Float" data-fieldname="over_transfer_allowance" placeholder="" data-doctype="Buying Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">Percentage you are allowed to transfer more against the quantity ordered. For example: If you have ordered 100 units. and your Allowance is 10% then you are allowed to transfer 110 units.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">over_transfer_allowance</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="section_break_xcug">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_5">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="auto_create_subcontracting_order">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="auto_create_subcontracting_order" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Auto Create Subcontracting Order</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Subcontracting Order (Draft) will be auto-created on submission of Purchase Order.</p>
                                </div>
                                <span class="tooltip-content">auto_create_subcontracting_order</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_izrr">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="auto_create_purchase_receipt">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="auto_create_purchase_receipt" placeholder="" data-doctype="Buying Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Auto Create Purchase Receipt</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Purchase Receipt (Draft) will be auto-created on submission of Subcontracting Receipt.</p>
                                </div>
                                <span class="tooltip-content">auto_create_purchase_receipt</span>
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