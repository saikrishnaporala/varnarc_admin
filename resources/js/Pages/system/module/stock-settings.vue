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
    <Head title="Accounts Settings" />
    <PageHeader title="Accounts Settings" pageTitle="Settings" subTitle="Module"/>
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
                            <div id="heatmap-stock_settings" class="heatmap"></div>
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
                      <div class="row form-section card-section visible-section" data-fieldname="item_defaults_section">
                        <div class="section-head">
                          Item Defaults
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_3">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="item_naming_by">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Item Naming By</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="Item Code">Item Code</option>
                                        <option value="Naming Series">Naming Series</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">Item Code</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">item_naming_by</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="valuation_method">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Valuation Method</label>
                                    <span class="help">
                                      <a href="https://docs.erpnext.com/docs/v14/user/manual/en/stock/articles/calculation-of-valuation-rate-in-fifo-and-moving-average" target="_blank" title="Documentation">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-help"></use>
                                        </svg>
                                      </a>
                                    </span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="FIFO">FIFO</option>
                                        <option value="Moving Average">Moving Average</option>
                                        <option value="LIFO">LIFO</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">FIFO</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">valuation_method</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="item_group">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Item Group</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Item Group" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
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
                                <span class="tooltip-content">item_group</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_4">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="default_warehouse">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Warehouse</label>
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
                                    <div class="control-value like-disabled-input" style="display: none;"><a href="/app/warehouse/Stores%20-%20POS" data-doctype="Warehouse" data-name="Stores - POS" data-value="Stores - POS">Stores - POS</a></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">default_warehouse</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="sample_retention_warehouse">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Sample Retention Warehouse</label>
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
                                <span class="tooltip-content">sample_retention_warehouse</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="stock_uom">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Default Stock UOM</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="UOM" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_5" role="combobox">
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
                                    <div class="control-value like-disabled-input" style="display: none;"><a href="/app/uom/Nos" data-doctype="UOM" data-name="Nos" data-value="Nos">Nos</a></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">stock_uom</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="price_list_defaults_section">
                        <div class="section-head">
                          Price List Defaults
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_4">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="auto_insert_price_list_rate_if_missing">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="auto_insert_price_list_rate_if_missing" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Auto Insert Item Price If Missing</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">auto_insert_price_list_rate_if_missing</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_12">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="update_existing_price_list_rate">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="update_existing_price_list_rate" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Update Existing Price List Rate</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">update_existing_price_list_rate</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="conversion_factor_section">
                        <div class="section-head">
                          Stock UOM Quantity
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_5">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_to_edit_stock_uom_qty_for_sales">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_to_edit_stock_uom_qty_for_sales" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow to Edit Stock UOM Qty for Sales Documents</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_to_edit_stock_uom_qty_for_sales</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_lznj">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_to_edit_stock_uom_qty_for_purchase">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_to_edit_stock_uom_qty_for_purchase" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow to Edit Stock UOM Qty for Purchase Documents</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_to_edit_stock_uom_qty_for_purchase</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Stock Validations">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_6">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="section_break_9">
                        <div class="section-head">
                          Stock Transactions Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_7">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Float" data-fieldname="over_delivery_receipt_allowance">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Over Delivery/Receipt Allowance (%)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Float" data-fieldname="over_delivery_receipt_allowance" placeholder="" data-doctype="Stock Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">The percentage you are allowed to receive or deliver more against the quantity ordered. For example, if you have ordered 100 units, and your Allowance is 10%, then you are allowed to receive 110 units.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">over_delivery_receipt_allowance</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Float" data-fieldname="mr_qty_allowance">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Over Transfer Allowance</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Float" data-fieldname="mr_qty_allowance" placeholder="" data-doctype="Stock Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">The percentage you are allowed to transfer more against the quantity ordered. For example, if you have ordered 100 units, and your Allowance is 10%, then you are allowed transfer 110 units.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">mr_qty_allowance</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Percent" data-fieldname="over_picking_allowance">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Over Picking Allowance</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Percent" data-fieldname="over_picking_allowance" placeholder="" data-doctype="Stock Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0%</div>
                                    <p class="help-box small text-muted">The percentage you are allowed to pick more items in the pick list than the ordered quantity.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">over_picking_allowance</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_121">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="role_allowed_to_over_deliver_receive">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Role Allowed to Over Deliver/Receive</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Role" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_6" role="combobox">
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
                                    <p class="help-box small text-muted">Users with this role are allowed to over deliver/receive against orders above the allowance percentage</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">role_allowed_to_over_deliver_receive</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_negative_stock">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_negative_stock" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Negative Stock</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_negative_stock</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_barcode_field">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_barcode_field" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Show Barcode Field in Stock Transactions</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">show_barcode_field</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="clean_description_html">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="clean_description_html" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Convert Item Description to Clean HTML in Transactions</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">clean_description_html</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_internal_transfer_at_arms_length_price">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_internal_transfer_at_arms_length_price" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Internal Transfers at Arm's Length Price</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If enabled, the item rate won't adjust to the valuation rate during internal transfers, but accounting will still use the valuation rate.</p>
                                </div>
                                <span class="tooltip-content">allow_internal_transfer_at_arms_length_price</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="quality_inspection_settings_section">
                        <div class="section-head">
                          Quality Inspection Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_8">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="action_if_quality_inspection_is_not_submitted">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Action If Quality Inspection Is Not Submitted</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="Stop">Stop</option>
                                        <option value="Warn">Warn</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">Stop</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">action_if_quality_inspection_is_not_submitted</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_23">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="action_if_quality_inspection_is_rejected">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Action If Quality Inspection Is Rejected</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="Stop">Stop</option>
                                        <option value="Warn">Warn</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">Stop</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">action_if_quality_inspection_is_rejected</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Stock Reservation">
                      <div class="row form-section card-section visible-section" data-fieldname="__section_9">
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_9">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_stock_reservation">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_stock_reservation" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Enable Stock Reservation</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Allows to keep aside a specific quantity of inventory for a particular order.</p>
                                </div>
                                <span class="tooltip-content">enable_stock_reservation</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_rx3e">
                            <form>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="allow_partial_reservation">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_partial_reservation" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Allow Partial Reservation</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Partial stock can be reserved. For example, If you have a Sales Order of 100 units and the Available Stock is 90 units then a Stock Reservation Entry will be created for 90 units. </p>
                                </div>
                                <span class="tooltip-content">allow_partial_reservation</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="auto_reserve_stock_for_sales_order_on_purchase">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="auto_reserve_stock_for_sales_order_on_purchase" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Auto Reserve Stock for Sales Order on Purchase</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Stock will be reserved on submission of <b>Purchase Receipt</b> created against Material Request for Sales Order.</p>
                                </div>
                                <span class="tooltip-content">auto_reserve_stock_for_sales_order_on_purchase</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section empty-section" data-fieldname="serial_and_batch_reservation_section">
                        <div class="section-head">
                          Serial and Batch Reservation
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_10">
                            <form>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="auto_reserve_serial_and_batch">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="auto_reserve_serial_and_batch" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Auto Reserve Serial and Batch Nos</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Serial and Batch Nos will be auto-reserved based on <b>Pick Serial / Batch Based On</b></p>
                                </div>
                                <span class="tooltip-content">auto_reserve_serial_and_batch</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Serial &amp; Batch Item">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_11">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="section_break_7">
                        <div class="section-head">
                          Serial &amp; Batch Item Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_12">
                            <form>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="do_not_use_batchwise_valuation">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="do_not_use_batchwise_valuation" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Do Not Use Batch-wise Valuation</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If enabled, the system will use the moving average valuation method to calculate the valuation rate for the batched items and will not consider the individual batch-wise incoming rate.</p>
                                </div>
                                <span class="tooltip-content">do_not_use_batchwise_valuation</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="auto_create_serial_and_batch_bundle_for_outward">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="auto_create_serial_and_batch_bundle_for_outward" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Auto Create Serial and Batch Bundle For Outward</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">auto_create_serial_and_batch_bundle_for_outward</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="pick_serial_and_batch_based_on">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label reqd" style="padding-right: 0px;">Pick Serial / Batch Based On</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold">
                                        <option value="FIFO">FIFO</option>
                                        <option value="LIFO">LIFO</option>
                                        <option value="Expiry">Expiry</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input bold" style="display: none;">FIFO</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">pick_serial_and_batch_based_on</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_mhzc">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_serial_no_and_batch_selector">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_serial_no_and_batch_selector" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Disable Serial No And Batch Selector</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">disable_serial_no_and_batch_selector</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="use_naming_series">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="use_naming_series" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Have Default Naming Series for Batch ID?</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">use_naming_series</span>
                              </div>
                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="naming_series_prefix">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Naming Series Prefix</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">BATCH-</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">naming_series_prefix</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="use_serial_batch_fields">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="use_serial_batch_fields" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Use Serial / Batch Fields</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">On submission of the stock transaction, system will auto create the Serial and Batch Bundle based on the Serial No / Batch fields.</p>
                                </div>
                                <span class="tooltip-content">use_serial_batch_fields</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="do_not_update_serial_batch_on_creation_of_auto_bundle">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="do_not_update_serial_batch_on_creation_of_auto_bundle" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Do Not Update Serial / Batch on Creation of Auto Bundle</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If enabled, do not update serial / batch values in the stock transactions on creation of auto Serial 
                                    / Batch Bundle. 
                                  </p>
                                </div>
                                <span class="tooltip-content">do_not_update_serial_batch_on_creation_of_auto_bundle</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Stock Planning">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_13">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="auto_material_request">
                        <div class="section-head">
                          Auto Material Request
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_14">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="auto_indent">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="auto_indent" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Raise Material Request When Stock Reaches Re-order Level</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">auto_indent</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_27">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="reorder_email_notify">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="reorder_email_notify" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Notify by Email on Creation of Automatic Material Request</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">reorder_email_notify</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="inter_warehouse_transfer_settings_section">
                        <div class="section-head">
                          Inter Warehouse Transfer Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_15">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_from_dn">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_from_dn" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Material Transfer from Delivery Note to Sales Invoice</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_from_dn</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_31">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_from_pr">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_from_pr" placeholder="" data-doctype="Stock Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow Material Transfer from Purchase Receipt to Purchase Invoice</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_from_pr</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Stock Closing">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_16">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="control_historical_stock_transactions_section">
                        <div class="section-head">
                          Control Historical Stock Transactions
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_17">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Date" data-fieldname="stock_frozen_upto">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Stock Frozen Upto</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Date" data-fieldname="stock_frozen_upto" placeholder="" data-doctype="Stock Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted">No stock transactions can be created or modified before this date.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">stock_frozen_upto</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="stock_frozen_upto_days">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Freeze Stocks Older Than (Days)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="stock_frozen_upto_days" placeholder="" data-doctype="Stock Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">Stock transactions that are older than the mentioned days cannot be modified.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">stock_frozen_upto_days</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_26">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="role_allowed_to_create_edit_back_dated_transactions">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Role Allowed to Create/Edit Back-dated Transactions</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Role" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_7" role="combobox">
                                          <ul hidden="" role="listbox" id="awesomplete_list_7"></ul>
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
                                    <p class="help-box small text-muted">If mentioned, the system will allow only the users with this Role to create or modify any stock transaction earlier than the latest stock transaction for a specific item and warehouse. If set as blank, it allows all users to create/edit back-dated transactions.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">role_allowed_to_create_edit_back_dated_transactions</span>
                              </div>
                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="stock_auth_role">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Role Allowed to Edit Frozen Stock</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Role" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_8" role="combobox">
                                          <ul hidden="" role="listbox" id="awesomplete_list_8"></ul>
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
                                    <p class="help-box small text-muted">The users with this Role are allowed to create/modify a stock transaction, even though the transaction is frozen.</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">stock_auth_role</span>
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