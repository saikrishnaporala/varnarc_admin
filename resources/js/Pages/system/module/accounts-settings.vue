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
    <Head title="Stock Settings" />
    <PageHeader title="Stock Settings" pageTitle="Settings" subTitle="Module"/>
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12" class="mt-3">
                  <div class="form-message hidden"></div>
                  <b-tabs v-model="activeTab" pills>
                    <b-tab title="Invoice and Billing">
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
                            <div id="heatmap-accounts_settings" class="heatmap"></div>
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
                      <div class="row form-section card-section visible-section" data-fieldname="audit_trail_section">
                        <div class="section-head collapsible">
                          Audit Trail
                          <span class="ml-2 collapse-indicator mb-1" style="">
                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                              <use class="mb-1" href="#es-line-up"></use>
                            </svg>
                          </span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_3">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_audit_trail">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_audit_trail" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Enable Audit Trail</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">In accordance with <a href="https://www.mca.gov.in/Ministry/pdf/AccountsAmendmentRules_24032021.pdf" target="_blank"> MCA Notification dated 24-03-2021</a>, enabling this feature will ensure that each change made to the books of account gets recorded. Once enabled, this feature cannot be disabled.</p>
                                </div>
                                <span class="tooltip-content">enable_audit_trail</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="enable_features_section">
                        <div class="section-head">
                          Invoice Cancellation
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_4">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="unlink_payment_on_cancellation_of_invoice">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="unlink_payment_on_cancellation_of_invoice" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Unlink Payment on Cancellation of Invoice</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">unlink_payment_on_cancellation_of_invoice</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="unlink_advance_payment_on_cancelation_of_order">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="unlink_advance_payment_on_cancelation_of_order" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Unlink Advance Payment on Cancellation of Order</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">unlink_advance_payment_on_cancelation_of_order</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_13">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="delete_linked_ledger_entries">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="delete_linked_ledger_entries" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Delete Accounting and Stock Ledger Entries on deletion of Transaction</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">delete_linked_ledger_entries</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_immutable_ledger">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_immutable_ledger" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Enable Immutable Ledger</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">On enabling this cancellation entries will be posted on the actual cancellation date and reports will consider cancelled entries as well</p>
                                </div>
                                <span class="tooltip-content">enable_immutable_ledger</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="invoicing_features_section">
                        <div class="section-head">
                          Invoicing Features
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_5">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="check_supplier_invoice_uniqueness">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="check_supplier_invoice_uniqueness" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Check Supplier Invoice Number Uniqueness</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Enabling this ensures each Purchase Invoice has a unique value in Supplier Invoice No. field within a particular fiscal year</p>
                                </div>
                                <span class="tooltip-content">check_supplier_invoice_uniqueness</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="automatically_fetch_payment_terms">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="automatically_fetch_payment_terms" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Automatically Fetch Payment Terms from Order</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Payment Terms from orders will be fetched into the invoices as is</p>
                                </div>
                                <span class="tooltip-content">automatically_fetch_payment_terms</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_17">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_common_party_accounting">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_common_party_accounting" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Enable Common Party Accounting</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Learn about <a href="https://docs.erpnext.com/docs/v13/user/manual/en/accounts/articles/common_party_accounting#:~:text=Common%20Party%20Accounting%20in%20ERPNext,Invoice%20against%20a%20primary%20Supplier.">Common Party</a></p>
                                </div>
                                <span class="tooltip-content">enable_common_party_accounting</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_multi_currency_invoices_against_single_party_account">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_multi_currency_invoices_against_single_party_account" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Allow multi-currency invoices against single party account </span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Enabling this will allow creation of multi-currency invoices against single party account in company currency</p>
                                </div>
                                <span class="tooltip-content">allow_multi_currency_invoices_against_single_party_account</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="journals_section">
                        <div class="section-head">
                          Journals
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_6">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="merge_similar_account_heads">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="merge_similar_account_heads" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Merge Similar Account Heads</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Rows with Same Account heads will be merged on Ledger</p>
                                </div>
                                <span class="tooltip-content">merge_similar_account_heads</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="deferred_accounting_settings_section">
                        <div class="section-head">
                          Deferred Accounting Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_7">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="book_deferred_entries_based_on">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Book Deferred Entries Based On</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="Days">Days</option>
                                        <option value="Months">Months</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">Days</div>
                                    <p class="help-box small text-muted">If "Months" is selected, a fixed amount will be booked as deferred revenue or expense for each month irrespective of the number of days in a month. It will be prorated if deferred revenue or expense is not booked for an entire month</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">book_deferred_entries_based_on</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_18">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="automatically_process_deferred_accounting_entry">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="automatically_process_deferred_accounting_entry" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Automatically Process Deferred Accounting Entry</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">automatically_process_deferred_accounting_entry</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="book_deferred_entries_via_journal_entry">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="book_deferred_entries_via_journal_entry" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Book Deferred Entries Via Journal Entry</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If this is unchecked, direct GL entries will be created to book deferred revenue or expense</p>
                                </div>
                                <span class="tooltip-content">book_deferred_entries_via_journal_entry</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="submit_journal_entries">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="submit_journal_entries" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Submit Journal Entries</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If this is unchecked Journal Entries will be saved in a Draft state and will have to be submitted manually</p>
                                </div>
                                <span class="tooltip-content">submit_journal_entries</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="tax_settings_section">
                        <div class="section-head">
                          Tax Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_8">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="determine_address_tax_category_from">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Determine Address Tax Category From</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input flex align-center">
                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                        <option value="Billing Address">Billing Address</option>
                                        <option value="Shipping Address">Shipping Address</option>
                                      </select>
                                      <div class="select-icon ">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-select"></use>
                                        </svg>
                                      </div>
                                    </div>
                                    <div class="control-value like-disabled-input" style="display: none;">Billing Address</div>
                                    <p class="help-box small text-muted">Address used to determine Tax Category in transactions</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">determine_address_tax_category_from</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_19">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="add_taxes_from_item_tax_template">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area" style="display: none;"></span>
                                  <span class="disp-area"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Automatically Add Taxes and Charges from Item Tax Template</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Overridden by India Compliance</p>
                                </div>
                                <span class="tooltip-content">add_taxes_from_item_tax_template</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="book_tax_discount_loss">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="book_tax_discount_loss" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Book Tax Loss on Early Payment Discount</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Split Early Payment Discount Loss into Income and Tax Loss</p>
                                </div>
                                <span class="tooltip-content">book_tax_discount_loss</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="round_row_wise_tax">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="round_row_wise_tax" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Round Tax Amount Row-wise</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Tax Amount will be rounded on a row(items) level</p>
                                </div>
                                <span class="tooltip-content">round_row_wise_tax</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="print_settings">
                        <div class="section-head">
                          Print Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_9">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_inclusive_tax_in_print">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_inclusive_tax_in_print" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Show Inclusive Tax in Print</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">show_inclusive_tax_in_print</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_taxes_as_table_in_print">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_taxes_as_table_in_print" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Show Taxes as Table in Print</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">show_taxes_as_table_in_print</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_12">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_payment_schedule_in_print">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_payment_schedule_in_print" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Show Payment Schedule in Print</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">show_payment_schedule_in_print</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="currency_exchange_section">
                        <div class="section-head">
                          Currency Exchange Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_10">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_stale">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_stale" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Allow Stale Exchange Rates</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">allow_stale</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="section_break_jpd0">
                        <div class="section-head">
                          Payment Reconciliations
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_11">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="auto_reconcile_payments">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="auto_reconcile_payments" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Auto Reconcile Payments</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">auto_reconcile_payments</span>
                              </div>
                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Int" data-fieldname="stale_days">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Stale Days</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="stale_days" placeholder="" data-doctype="Accounts Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">1</div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">stale_days</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Credit Limits">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_12">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="accounts_transactions_settings_section">
                        <div class="section-head">
                          Credit Limit Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_13">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Currency" data-fieldname="over_billing_allowance">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Over Billing Allowance (%)</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Currency" data-fieldname="over_billing_allowance" placeholder="" data-doctype="Accounts Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">₹ 0.00</div>
                                    <p class="help-box small text-muted">The percentage you are allowed to bill more against the amount ordered. For example, if the order value is $100 for an item and tolerance is set as 10%, then you are allowed to bill up to $110 </p>
                                  </div>
                                </div>
                                <span class="tooltip-content">over_billing_allowance</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_11">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="role_allowed_to_over_bill">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Role Allowed to Over Bill </label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Role" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
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
                                    <p class="help-box small text-muted">Users with this role are allowed to over bill above the allowance percentage</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">role_allowed_to_over_bill</span>
                              </div>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="credit_controller">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Role allowed to bypass Credit Limit</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input">
                                      <div class="link-field ui-front" style="position: relative;">
                                        <div class="awesomplete">
                                          <input type="text" class="input-with-feedback form-control" data-target="Role" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
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
                                <span class="tooltip-content">credit_controller</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="make_payment_via_journal_entry">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"></span>
                                  <span class="disp-area"></span>
                                  <span class="label-area"></span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">make_payment_via_journal_entry</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="POS">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_14">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="pos_setting_section">
                        <div class="section-head">
                          POS Setting
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_15">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="post_change_gl_entries">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="post_change_gl_entries" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Create Ledger Entries for Change Amount</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">If enabled, ledger entries will be posted for change amount in POS transactions</p>
                                </div>
                                <span class="tooltip-content">post_change_gl_entries</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Assets">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_16">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="asset_settings_section">
                        <div class="section-head">
                          Asset Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_17">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="calculate_depr_using_total_days">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="calculate_depr_using_total_days" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Calculate daily depreciation using total days in depreciation period</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Enable this option to calculate daily depreciation by considering the total number of days in the entire depreciation period, (including leap years) while using daily pro-rata based depreciation</p>
                                </div>
                                <span class="tooltip-content">calculate_depr_using_total_days</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_gjcc">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="book_asset_depreciation_entry_automatically">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="book_asset_depreciation_entry_automatically" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Book Asset Depreciation Entry Automatically</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">book_asset_depreciation_entry_automatically</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Accounts Closing">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_18">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="period_closing_settings_section">
                        <div class="section-head">
                          Period Closing Settings
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_19">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Date" data-fieldname="acc_frozen_upto">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Accounts Frozen Till Date</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Date" data-fieldname="acc_frozen_upto" placeholder="" data-doctype="Accounts Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted">Accounting entries are frozen up to this date. Nobody can create or modify entries except users with the role specified below</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">acc_frozen_upto</span>
                              </div>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="ignore_account_closing_balance">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="ignore_account_closing_balance" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Ignore Account Closing Balance</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Financial reports will be generated using GL Entry doctypes (should be enabled if Period Closing Voucher is not posted for all years sequentially or missing) </p>
                                </div>
                                <span class="tooltip-content">ignore_account_closing_balance</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_25">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="frozen_accounts_modifier">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Role Allowed to Set Frozen Accounts and Edit Frozen Entries</label>
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
                                    <p class="help-box small text-muted">Users with this role are allowed to set frozen accounts and create / modify accounting entries against frozen accounts</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">frozen_accounts_modifier</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Chart Of Accounts">
                      <div class="row form-section card-section visible-section" data-fieldname="__section_20">
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_20">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_balance_in_coa">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_balance_in_coa" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Show Balances in Chart Of Accounts</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">show_balance_in_coa</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Banking">
                      <div class="row form-section card-section visible-section" data-fieldname="__section_21">
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_21">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_party_matching">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_party_matching" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Enable Automatic Party Matching</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Auto match and set the Party in Bank Transactions</p>
                                </div>
                                <span class="tooltip-content">enable_party_matching</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="enable_fuzzy_matching">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_fuzzy_matching" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Enable Fuzzy Matching</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted">Approximately match the description/party name against parties</p>
                                </div>
                                <span class="tooltip-content">enable_fuzzy_matching</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Reports">
                      <div class="row form-section card-section empty-section" data-fieldname="__section_22">
                        <div class="section-body"></div>
                      </div>
                      <div class="row form-section card-section visible-section" data-fieldname="remarks_section">
                        <div class="section-head">
                          Remarks Column Length
                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                        </div>
                        <div class="section-body">
                          <div class="form-column col-sm-6" data-fieldname="__column_23">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="general_ledger_remarks_length">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">General Ledger</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="general_ledger_remarks_length" placeholder="" data-doctype="Accounts Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">Truncates 'Remarks' column to set character length</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">general_ledger_remarks_length</span>
                              </div>
                            </form>
                          </div>
                          <div class="form-column col-sm-6" data-fieldname="column_break_lvjk">
                            <form>
                              <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="receivable_payable_remarks_length">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;">Accounts Receivable/Payable</label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="receivable_payable_remarks_length" placeholder="" data-doctype="Accounts Settings"></div>
                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                    <p class="help-box small text-muted">Truncates 'Remarks' column to set character length</p>
                                  </div>
                                </div>
                                <span class="tooltip-content">receivable_payable_remarks_length</span>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Payment Request">
                      <div class="row form-section card-section visible-section" data-fieldname="__section_24">
                        <div class="section-body">
                          <div class="form-column col-sm-12" data-fieldname="__column_24">
                            <form>
                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="create_pr_in_draft_status">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="create_pr_in_draft_status" placeholder="" data-doctype="Accounts Settings"></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                  <span class="label-area">Create in Draft Status</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">create_pr_in_draft_status</span>
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