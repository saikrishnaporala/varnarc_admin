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
    <Head title="Notifications" />
    <PageHeader title="Notifications" pageTitle="Emails/Notifications"/>
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12" class="mt-3">
                  <div class="form-message hidden"></div>
                  <div class="container page-body">
                    <div class="page-toolbar hide">
                      <div class="container">   </div>
                    </div>
                    <div class="page-wrapper">
                      <div class="page-content">
                        <div class="workflow-button-area btn-group pull-right hide"></div>
                        <div class="clearfix"></div>
                        <div class="row layout-main" style="">
                          <div class="col-lg-2 layout-side-section">
                            <div class="list-sidebar overlay-sidebar hidden-xs hidden-sm">
                              <ul class="list-unstyled sidebar-menu user-actions hide">
                                <li class="divider"></li>
                              </ul>
                              <ul class="list-unstyled sidebar-menu">
                                <div class="sidebar-section views-section hide">
                                  <li class="sidebar-label">   </li>
                                  <div class="current-view">
                                    <li class="list-link">
                                      <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                                        <span class="selected-view ellipsis">      </span>      
                                        <span>
                                          <svg class="icon icon-xs">
                                            <use href="#icon-select"></use>
                                          </svg>
                                        </span>
                                      </a>
                                      <ul class="dropdown-menu views-dropdown" role="menu">     </ul>
                                    </li>
                                    <li class="sidebar-action">     <a class="view-action"></a>    </li>
                                  </div>
                                </div>
                                <div class="sidebar-section filter-section">
                                  <li class="sidebar-label">    Filter By   </li>
                                  <div class="list-group-by">
                                    <div class="list-group-by-fields">
                                      <li class="group-by-field list-link">
                                        <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-label="Assigned To" data-fieldname="assigned_to" data-fieldtype="undefined" href="#" onclick="return false;">
                                          <span class="ellipsis">Assigned To</span>
                                          <span>
                                            <svg class="icon  icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#icon-select"></use>
                                            </svg>
                                          </span>
                                        </a>
                                        <ul class="dropdown-menu group-by-dropdown" role="menu">
                                        </ul>
                                      </li>
                                      <li class="group-by-field list-link">
                                        <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-label="Created By" data-fieldname="owner" data-fieldtype="undefined" href="#" onclick="return false;">
                                          <span class="ellipsis">Created By</span>
                                          <span>
                                            <svg class="icon  icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#icon-select"></use>
                                            </svg>
                                          </span>
                                        </a>
                                        <ul class="dropdown-menu group-by-dropdown" role="menu">
                                        </ul>
                                      </li>
                                    </div>
                                    <li class="add-list-group-by sidebar-action">
                                      <a class="add-group-by">
                                      Edit Filters
                                      </a>
                                    </li>
                                  </div>
                                  <div class="list-tags">
                                    <li class="list-stats list-link">
                                      <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                                        <span>Tags</span>      
                                        <span>
                                          <svg class="icon icon-xs">
                                            <use href="#icon-select"></use>
                                          </svg>
                                        </span>
                                      </a>
                                      <ul class="dropdown-menu list-stats-dropdown" role="menu">
                                        <div class="dropdown-search">       <input type="text" placeholder="Search" data-element="search" class="form-control input-xs">      </div>
                                        <div class="stat-result">      </div>
                                      </ul>
                                    </li>
                                    <li class="sidebar-action show-tags">     <a class="list-tag-preview">Show Tags</a>    </li>
                                  </div>
                                </div>
                                <div class="sidebar-section save-filter-section">
                                  <li class="sidebar-label">    Save Filter   </li>
                                  <li class="list-filters list-link">
                                  <li class="input-area">
                                    <div class="frappe-control input-max-width" data-fieldtype="Data">
                                      <div class="form-group">
                                        <div class="clearfix">
                                          <label class="control-label" style="padding-right: 0px;"></label>
                                          <span class="help"></span>
                                        </div>
                                        <div class="control-input-wrapper">
                                          <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" placeholder="Filter Name"></div>
                                          <div class="control-value like-disabled-input" style="display: none;"></div>
                                          <p class="help-box small text-muted"></p>
                                        </div>
                                      </div>
                                      <span class="tooltip-content">undefined</span>
                                    </div>
                                    <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check">
                                      <div class="checkbox">
                                        <label>
                                        <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" placeholder=""></span>
                                        <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                        <span class="label-area">Is Global</span>
                                        <span class="ml-1 help"></span>
                                        </label>
                                        <p class="help-box small text-muted"></p>
                                      </div>
                                      <span class="tooltip-content">undefined</span>
                                    </div>
                                  </li>
                                  <li class="sidebar-action">
                                    <a class="saved-filters-preview" style="display: none;">Hide Saved</a>
                                  </li>
                                  <div class="saved-filters" style=""></div>
                                  </li> 
                                </div>
                              </ul>
                            </div>
                          </div>
                          <div class="col layout-main-section-wrapper">
                            <div class="layout-main-section frappe-card">
                              <div class="page-form flex">
                                <div class="standard-filter-section flex">
                                  <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="name" title="" data-original-title="ID"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="name" placeholder="ID"><span class="tooltip-content">name</span></div>
                                  <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Link" data-fieldname="module" title="" data-original-title="Module">
                                    <div class="link-field ui-front" style="position: relative;">
                                      <div class="awesomplete">
                                        <input type="text" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Link" data-fieldname="module" placeholder="Module" data-target="Module Def" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
                                        <ul hidden="" role="listbox" id="awesomplete_list_2"></ul>
                                        <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                      </div>
                                    </div>
                                    <span class="tooltip-content">module</span>
                                  </div>
                                  <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="subject" title="" data-original-title="Subject"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="subject" placeholder="Subject"><span class="tooltip-content">subject</span></div>
                                  <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Link" data-fieldname="document_type" title="" data-original-title="Document Type">
                                    <div class="link-field ui-front" style="position: relative;">
                                      <div class="awesomplete">
                                        <input type="text" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Link" data-fieldname="document_type" placeholder="Document Type" data-target="DocType" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
                                        <ul hidden="" role="listbox" id="awesomplete_list_3"></ul>
                                        <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                      </div>
                                    </div>
                                    <span class="tooltip-content">document_type</span>
                                  </div>
                                </div>
                                <div class="filter-section flex">
                                  <div class="filter-selector">
                                    <div class="btn-group">
                                      <button class="btn btn-default btn-sm filter-button" data-original-title="" title="">
                                        <span class="filter-icon">
                                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#es-line-filter"></use>
                                          </svg>
                                        </span>
                                        <span class="button-label hidden-xs">
                                        Filter
                                        <span>
                                        </span></span>
                                      </button>
                                      <button class="btn btn-default btn-sm filter-x-button" title="Clear all filters">
                                        <span class="filter-icon">
                                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#es-small-close"></use>
                                          </svg>
                                        </span>
                                      </button>
                                    </div>
                                  </div>
                                  <div class="sort-selector">
                                    <div class="btn-group">
                                      <button class="btn btn-default btn-sm btn-order" data-value="desc" title="descending">
                                        <span class="sort-order">
                                          <svg class="icon icon-sm">
                                            <use href="#icon-sort-descending"></use>
                                          </svg>
                                        </span>
                                      </button>
                                      <button type="button" class="btn btn-default btn-sm sort-selector-button" data-toggle="dropdown">
                                        <span class="dropdown-text">Last Updated On</span>    
                                        <ul class="dropdown-menu dropdown-menu-right">
                                          <li>      <a class="dropdown-item option" data-value="modified">       Last Updated On      </a>     </li>
                                          <li>      <a class="dropdown-item option" data-value="subject">       Subject      </a>     </li>
                                          <li>      <a class="dropdown-item option" data-value="name">       ID      </a>     </li>
                                          <li>      <a class="dropdown-item option" data-value="creation">       Created On      </a>     </li>
                                          <li>      <a class="dropdown-item option" data-value="idx">       Most Used      </a>     </li>
                                          <li>      <a class="dropdown-item option" data-value="channel">       Channel      </a>     </li>
                                          <li>      <a class="dropdown-item option" data-value="event">       Send Alert On      </a>     </li>
                                          <li>      <a class="dropdown-item option" data-value="document_type">       Document Type      </a>     </li>
                                          <li>      <a class="dropdown-item option" data-value="condition">       Condition      </a>     </li>
                                        </ul>
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="frappe-list">
                                <div class="result">
                                  <header class="level list-row-head text-muted">
                                    <div class="level-left list-header-subject">
                                      <div class="list-row-col ellipsis list-subject level  ">
                                        <input class="level-item list-check-all" type="checkbox" title="Select All">
                                        <span class="level-item" data-sort-by="subject" title="Click to sort by Subject">
                                        Subject
                                        </span>
                                      </div>
                                      <div class="list-row-col ellipsis hidden-xs tag-col hide "><span>Tag</span></div>
                                      <div class="list-row-col ellipsis hidden-xs  "><span>Status</span></div>
                                      <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="event" title="Click to sort by Send Alert On">Send Alert On</span></div>
                                      <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="document_type" title="Click to sort by Document Type">Document Type</span></div>
                                      <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="condition" title="Click to sort by Condition">Condition</span></div>
                                      <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="name" title="Click to sort by ID">ID</span></div>
                                    </div>
                                    <div class="level-left checkbox-actions">
                                      <div class="level list-subject">
                                        <input class="level-item list-check-all" type="checkbox" title="Select All">
                                        <span class="level-item list-header-meta"></span>
                                      </div>
                                    </div>
                                    <div class="level-right">
                                      <span class="list-count"><span>2 of 2</span></span>
                                      <span class="level-item list-liked-by-me hidden-xs">
                                        <span title="Liked by me">
                                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                            <use class="like-icon" href="#es-solid-heart"></use>
                                          </svg>
                                        </span>
                                      </span>
                                    </div>
                                  </header>
                                  <div class="freeze flex justify-center align-center text-muted" style="display: none;">
                                    Loading...
                                  </div>
                                  <div class="list-row-container" tabindex="1">
                                    <div class="level list-row">
                                      <div class="level-left ellipsis">
                                        <div class="list-row-col ellipsis list-subject level ">
                                          <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Notification" data-name="Material Request Receipt Notification"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Notification" data-name="Material Request Receipt Notification" href="/app/notification/Material%20Request%20Receipt%20Notification" title="{{ doc.name }} has been received">{{ doc.name }} has been received</a></span>
                                        </div>
                                        <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                          <div class="tags-empty">-</div>
                                        </div>
                                        <div class="list-row-col hidden-xs ellipsis">
                                          <span class="indicator-pill blue filterable no-indicator-dot ellipsis" data-filter="enabled,=,1" title="Document is in draft state">
                                          <span class="ellipsis"> Enabled</span>
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs ">
                                          <span class="ellipsis" title="Send Alert On: Value Change">
                                          <span class="filterable indicator-pill gray ellipsis" data-filter="event,=,Value Change">
                                          <span class="ellipsis"> Value Change </span>
                                          </span>
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs ">
                                          <span class="ellipsis" title="Document Type: Material Request">
                                          <a class="filterable ellipsis" data-filter="document_type,=,Material Request">
                                          Material Request
                                          </a>
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs ">
                                          <span class="ellipsis" title="Condition: doc.status &amp;#x3D;&amp;#x3D; &amp;quot;Received&amp;quot; or doc.status &amp;#x3D;&amp;#x3D; &amp;quot;Partially Received&amp;quot;">
                                          <span class="ellipsis">
                                          doc.status == "Received" or doc.status == "Partially Received"
                                          </span>
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs ">
                                          <span class="ellipsis" title="ID: Material Request Receipt Notification">
                                          <a class="filterable ellipsis" data-filter="name,=,Material Request Receipt Notification">
                                          Material Request Receipt Notification
                                          </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="level-right text-muted ellipsis">
                                        <div class="level-item list-row-activity hidden-xs">
                                          <div class="hidden-md hidden-xs">
                                          </div>
                                          <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2019-05-01 18:02:51.090037" title="01-05-2019 18:02:51">5 y</span></span>
                                          <span class="comment-count d-flex align-items-center">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-chat-alt"></use>
                                            </svg>
                                            0
                                          </span>
                                          <span class="mx-2">·</span>
                                          <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                            <span class="like-action not-liked" data-doctype="Notification" data-name="Material Request Receipt Notification" data-liked-by="" title="">
                                              <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                <use class="like-icon" href="#es-solid-heart"></use>
                                              </svg>
                                            </span>
                                          </span>
                                        </div>
                                        <div class="level-item visible-xs text-right">
                                          <span class="indicator-pill blue filterable no-indicator-dot ellipsis" data-filter="enabled,=,1" title="Document is in draft state">
                                          <span class="ellipsis"> Enabled</span>
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="list-row-border"></div>
                                  </div>
                                  <div class="list-row-container" tabindex="1">
                                    <div class="level list-row">
                                      <div class="level-left ellipsis">
                                        <div class="list-row-col ellipsis list-subject level ">
                                          <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Notification" data-name="Notification for new fiscal year"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Notification" data-name="Notification for new fiscal year" href="/app/notification/Notification%20for%20new%20fiscal%20year" title="Notification for new fiscal year {{ doc.name }}">Notification for new fiscal year {{ doc.name }}</a></span>
                                        </div>
                                        <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                          <div class="tags-empty">-</div>
                                        </div>
                                        <div class="list-row-col hidden-xs ellipsis">
                                          <span class="indicator-pill blue filterable no-indicator-dot ellipsis" data-filter="enabled,=,1" title="Document is in draft state">
                                          <span class="ellipsis"> Enabled</span>
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs ">
                                          <span class="ellipsis" title="Send Alert On: New">
                                          <span class="filterable indicator-pill gray ellipsis" data-filter="event,=,New">
                                          <span class="ellipsis"> New </span>
                                          </span>
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs ">
                                          <span class="ellipsis" title="Document Type: Fiscal Year">
                                          <a class="filterable ellipsis" data-filter="document_type,=,Fiscal Year">
                                          Fiscal Year
                                          </a>
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs ">
                                          <span class="ellipsis" title="Condition: doc.auto_created">
                                          <span class="ellipsis">
                                          doc.auto_created
                                          </span>
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs ">
                                          <span class="ellipsis" title="ID: Notification for new fiscal year">
                                          <a class="filterable ellipsis" data-filter="name,=,Notification for new fiscal year">
                                          Notification for new fiscal year
                                          </a>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="level-right text-muted ellipsis">
                                        <div class="level-item list-row-activity hidden-xs">
                                          <div class="hidden-md hidden-xs">
                                          </div>
                                          <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2018-04-25 14:30:38.588534" title="25-04-2018 14:30:38">6 y</span></span>
                                          <span class="comment-count d-flex align-items-center">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-chat-alt"></use>
                                            </svg>
                                            0
                                          </span>
                                          <span class="mx-2">·</span>
                                          <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                            <span class="like-action not-liked" data-doctype="Notification" data-name="Notification for new fiscal year" data-liked-by="" title="">
                                              <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                <use class="like-icon" href="#es-solid-heart"></use>
                                              </svg>
                                            </span>
                                          </span>
                                        </div>
                                        <div class="level-item visible-xs text-right">
                                          <span class="indicator-pill blue filterable no-indicator-dot ellipsis" data-filter="enabled,=,1" title="Document is in draft state">
                                          <span class="ellipsis"> Enabled</span>
                                          </span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="list-row-border"></div>
                                  </div>
                                </div>
                                <div class="no-result text-muted flex justify-center align-center" style="display: none;">
                                  <div class="no-result text-muted flex justify-center align-center">
                                    <div class="msg-box no-border">
                                      <div>
                                        <img src="/assets/frappe/images/ui-states/list-empty-state.svg" alt="Generic Empty State" class="null-state">
                                      </div>
                                      <p>You haven't created a Notification yet</p>
                                      <p><button class="btn btn-default btn-sm btn-new-doc hidden-xs">
                                        Create your first Notification
                                        </button> <button class="btn btn-primary btn-new-doc visible-xs">
                                        Create New
                                        </button>
                                      </p>
                                    </div>
                                  </div>
                                </div>
                                <div class="list-paging-area level" style="">
                                  <div class="level-left">
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-default btn-sm btn-paging btn-info" data-value="20">
                                      20
                                      </button>
                                      <button type="button" class="btn btn-default btn-sm btn-paging" data-value="100">
                                      100
                                      </button>
                                      <button type="button" class="btn btn-default btn-sm btn-paging" data-value="500">
                                      500
                                      </button>
                                      <button type="button" class="btn btn-default btn-sm btn-paging" data-value="2500">
                                      2500
                                      </button>
                                    </div>
                                  </div>
                                  <div class="level-right">
                                    <button class="btn btn-default btn-more btn-sm" style="display: none;">
                                    Load More
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="layout-footer hide"></div>
                          </div>
                        </div>
                        <div class="row list-skeleton" style="display: none;">
                          <div class="col-lg-2">
                            <div class="list-skeleton-box"></div>
                          </div>
                          <div class="col">
                            <div class="list-skeleton-box"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="container page-body">
                    <div class="page-toolbar hide">
                      <div class="container">   </div>
                    </div>
                    <div class="page-wrapper">
                      <div class="page-content">
                        <div class="workflow-button-area btn-group pull-right hide"></div>
                        <div class="clearfix"></div>
                        <div class="row layout-main">
                          <div class="col-lg-2 layout-side-section hide-sidebar">
                            <div class="form-sidebar overlay-sidebar hidden-xs hidden-sm" style="display: none;">
                              <ul class="list-unstyled sidebar-menu user-actions hidden"></ul>
                              <ul class="list-unstyled sidebar-menu sidebar-image-section hide">
                                <li class="sidebar-image-wrapper">
                                  <img class="sidebar-image">   
                                  <div class="sidebar-standard-image">
                                    <div class="standard-image"></div>
                                  </div>
                                  <div class="sidebar-image-actions">
                                    <div class="dropdown">
                                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Change</a>     
                                      <div class="dropdown-menu" role="menu">      <a class="dropdown-item sidebar-image-change">Upload</a>      <a class="dropdown-item sidebar-image-remove">Remove</a>     </div>
                                    </div>
                                  </div>
                                </li>
                              </ul>
                              <ul class="list-unstyled sidebar-menu sidebar-rating hide">
                                <li style="position: relative;">   <a class="strong badge-hover">    <span>Feedback</span>   </a>  </li>
                                <li class="rating-icons"></li>
                              </ul>
                              <ul class="list-unstyled sidebar-menu hidden">   </ul>
                              <ul class="list-unstyled sidebar-menu form-assignments" style="display: none;">
                                <li>
                                  <span class="form-sidebar-items">
                                    <span class="add-assignment-label">
                                      <svg class="es-icon ml-0 icon-sm">
                                        <use href="#es-line-add-people"></use>
                                      </svg>
                                      <span class="ellipsis">Assigned To</span>    
                                    </span>
                                    <button class="add-assignment-btn btn btn-link icon-btn">
                                      <svg class="es-icon icon-sm">
                                        <use href="#es-line-add"></use>
                                      </svg>
                                    </button>
                                  </span>
                                  <div class="assignments"></div>
                                </li>
                              </ul>
                              <ul class="list-unstyled sidebar-menu form-attachments">
                                <li class="attachments-actions">
                                  <span class="form-sidebar-items">
                                    <span>
                                      <svg class="es-icon ml-0 icon-sm">
                                        <use href="#es-line-attachment"></use>
                                      </svg>
                                      <a class="pill-label ellipsis explore-link">      Attachments     </a>    
                                    </span>
                                    <button class="add-attachment-btn btn btn-link icon-btn">
                                      <svg class="es-icon icon-sm">
                                        <use href="#es-line-add"></use>
                                      </svg>
                                    </button>
                                  </span>
                                </li>
                                <a class="show-all-btn hidden" href="">
                                  <svg class="es-icon icon-sm">
                                    <use href="#es-line-preview"></use>
                                  </svg>
                                  <span class="pill-label ellipsis">    Show All   </span>  
                                </a>
                              </ul>
                              <ul class="list-unstyled sidebar-menu form-tags">
                                <li>
                                  <span class="form-sidebar-items">
                                    <span>
                                      <svg class="es-icon ml-0 icon-sm">
                                        <use href="#es-line-tag"></use>
                                      </svg>
                                      <span class="tags-label ellipsis">Tags</span>     
                                    </span>
                                    <button class="add-tags-btn text-muted btn btn-link icon-btn" id="add_tags">
                                      <svg class="es-icon icon-sm">
                                        <use href="#es-line-add"></use>
                                      </svg>
                                    </button>
                                  </span>
                                </li>
                                <li class="" style="display: none;">
                                  <div class="awesomplete">
                                    <input class="tags-input form-control" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_4" role="combobox">
                                    <ul hidden="" role="listbox" id="awesomplete_list_4"></ul>
                                    <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                  </div>
                                </li>
                              </ul>
                              <ul class="list-unstyled sidebar-menu form-shared">
                                <li>
                                  <span class="form-sidebar-items">
                                    <span class="share-label">
                                      <svg class="es-icon ml-0 icon-sm">
                                        <use href="#es-line-add-people"></use>
                                      </svg>
                                      <span class="ellipsis">Share</span>    
                                    </span>
                                    <button class="share-doc-btn btn btn-link icon-btn">
                                      <svg class="es-icon icon-sm">
                                        <use href="#es-line-add"></use>
                                      </svg>
                                    </button>
                                  </span>
                                  <div class="shares"></div>
                                </li>
                              </ul>
                              <ul class="list-unstyled sidebar-menu followed-by-section">
                                <li class="sidebar-label followed-by-label hidden">
                                  <svg class="icon icon-sm">
                                    <use href="#icon-link-url" class="like-icon"></use>
                                  </svg>
                                  Followed by  
                                </li>
                                <li class="followed-by"></li>
                                <li class="document-follow">   <a class="badge-hover follow-document-link hidden">    Follow   </a>   <a class="badge-hover unfollow-document-link hidden">    Unfollow   </a>  </li>
                              </ul>
                              <ul class="list-unstyled sidebar-menu">
                                <a>
                                  <li class="auto-repeat-status"></li>
                                </a>
                                <li><a></a> </li>
                              </ul>
                              <ul class="list-unstyled sidebar-menu form-sidebar-stats">
                                <li class="flex">
                                  <div class="form-stats d-flex">
                                    <span class="form-stats-likes">
                                      <span class="liked-by like-action d-flex align-items-center">
                                        <svg class="es-icon icon-sm">
                                          <use href="#es-solid-heart" class="like-icon"></use>
                                        </svg>
                                        <span class="like-count ml-2"></span>     
                                      </span>
                                    </span>
                                    <span class="mx-2">·</span>    
                                    <a class="comments d-flex align-items-center">
                                      <svg class="es-icon icon-sm">
                                        <use href="#es-line-chat-alt" class="comment-icon"></use>
                                      </svg>
                                      <span class="comments-count ml-2"></span>    
                                    </a>
                                  </div>
                                  <a class="form-follow text-sm">    Follow   </a>  
                                </li>
                              </ul>
                              <hr>
                              <ul class="list-unstyled sidebar-menu text-muted">
                                <li class="pageview-count"></li>
                                <li class="modified-by"></li>
                                <li class="created-by"></li>
                              </ul>
                            </div>
                          </div>
                          <div class="col layout-main-section-wrapper">
                            <div class="layout-main-section">
                              <div class="page-form row hide"></div>
                              <div>
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
                                            <div id="heatmap-notification" class="heatmap"></div>
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
                                      <div class="row form-section card-section visible-section" data-fieldname="__section_1">
                                        <div class="section-body">
                                          <div class="form-column col-sm-6" data-fieldname="__column_1">
                                            <form>
                                              <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="__newname">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label reqd" style="padding-right: 0px;">Name</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Data" data-fieldname="__newname" placeholder="" data-doctype="Notification"></div>
                                                    <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">__newname</span>
                                              </div>
                                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enabled">
                                                <div class="checkbox">
                                                  <label>
                                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enabled" placeholder="" data-doctype="Notification"></span>
                                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                                  <span class="label-area">Enabled</span>
                                                  <span class="ml-1 help"></span>
                                                  </label>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                                <span class="tooltip-content">enabled</span>
                                              </div>
                                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="is_standard">
                                                <div class="checkbox">
                                                  <label>
                                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="is_standard" placeholder="" data-doctype="Notification"></span>
                                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                  <span class="label-area">Is Standard</span>
                                                  <span class="ml-1 help"></span>
                                                  </label>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                                <span class="tooltip-content">is_standard</span>
                                              </div>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="module">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Module</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input">
                                                      <div class="link-field ui-front" style="position: relative;">
                                                        <div class="awesomplete">
                                                          <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="module" placeholder="" data-doctype="Notification" data-target="Module Def" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_5" role="combobox">
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
                                                <span class="tooltip-content">module</span>
                                              </div>
                                            </form>
                                          </div>
                                          <div class="form-column col-sm-6" data-fieldname="column_break_2">
                                            <form>
                                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="channel">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label reqd" style="padding-right: 0px;">Channel</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input flex align-center">
                                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="channel" placeholder="" data-doctype="Notification">
                                                        <option value="Email">Email</option>
                                                        <option value="Slack">Slack</option>
                                                        <option value="System Notification">System Notification</option>
                                                        <option value="SMS">SMS</option>
                                                      </select>
                                                      <div class="select-icon ">
                                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                          <use class="" href="#icon-select"></use>
                                                        </svg>
                                                      </div>
                                                    </div>
                                                    <div class="control-value like-disabled-input bold" style="display: none;">Email</div>
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">channel</span>
                                              </div>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="slack_webhook_url">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Slack Channel</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input">
                                                      <div class="link-field ui-front" style="position: relative;">
                                                        <div class="awesomplete">
                                                          <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="slack_webhook_url" placeholder="" data-doctype="Notification" data-target="Slack Webhook URL" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_6" role="combobox">
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
                                                    <p class="help-box small text-muted">To use Slack Channel, add a <a href="#List/Slack%20Webhook%20URL/List">Slack Webhook URL</a>.</p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">slack_webhook_url</span>
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row form-section card-section visible-section" data-fieldname="filters">
                                        <div class="section-head">
                                          Filters
                                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                        </div>
                                        <div class="section-body">
                                          <div class="form-column col-sm-6" data-fieldname="__column_2">
                                            <form>
                                              <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="subject">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label reqd" style="padding-right: 0px;">Subject</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Data" data-fieldname="subject" placeholder="" data-doctype="Notification"></div>
                                                    <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                                    <p class="help-box small text-muted">To add dynamic subject, use jinja tags like
                                                    <div>
                                                      <pre><code>{{ doc.name }} Delivered</code></pre>
                                                    </div>
                                                    </p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">subject</span>
                                              </div>
                                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="event">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label reqd" style="padding-right: 0px;">Send Alert On</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input flex align-center">
                                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="event" placeholder="" data-doctype="Notification">
                                                        <option></option>
                                                        <option value="New">New</option>
                                                        <option value="Save">Save</option>
                                                        <option value="Submit">Submit</option>
                                                        <option value="Cancel">Cancel</option>
                                                        <option value="Days After">Days After</option>
                                                        <option value="Days Before">Days Before</option>
                                                        <option value="Value Change">Value Change</option>
                                                        <option value="Method">Method</option>
                                                        <option value="Custom">Custom</option>
                                                      </select>
                                                      <div class="select-icon ">
                                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                          <use class="" href="#icon-select"></use>
                                                        </svg>
                                                      </div>
                                                    </div>
                                                    <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">event</span>
                                              </div>
                                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="document_type">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label reqd" style="padding-right: 0px;">Document Type</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input">
                                                      <div class="link-field ui-front" style="position: relative;">
                                                        <div class="awesomplete">
                                                          <input type="text" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Link" data-fieldname="document_type" placeholder="" data-doctype="Notification" data-target="DocType" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_7" role="combobox">
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
                                                    <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">document_type</span>
                                              </div>
                                            </form>
                                          </div>
                                          <div class="form-column col-sm-6" data-fieldname="col_break_1">
                                            <form>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="method">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Trigger Method</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="method" placeholder="" data-doctype="Notification"></div>
                                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                                    <p class="help-box small text-muted">Trigger on valid methods like "before_insert", "after_update", etc (will depend on the DocType selected)</p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">method</span>
                                              </div>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Select" data-fieldname="date_changed">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Reference Date</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input flex align-center">
                                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="date_changed" placeholder="" data-doctype="Notification"></select>
                                                      <div class="select-icon ">
                                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                          <use class="" href="#icon-select"></use>
                                                        </svg>
                                                      </div>
                                                    </div>
                                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                                    <p class="help-box small text-muted">Send alert if date matches this field's value</p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">date_changed</span>
                                              </div>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Int" data-fieldname="days_in_advance">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Days Before or After</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="days_in_advance" placeholder="" data-doctype="Notification"></div>
                                                    <div class="control-value like-disabled-input" style="display: none;">0</div>
                                                    <p class="help-box small text-muted">Send days before or after the reference date</p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">days_in_advance</span>
                                              </div>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Select" data-fieldname="value_changed">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Value Changed</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input flex align-center">
                                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="value_changed" placeholder="" data-doctype="Notification"></select>
                                                      <div class="select-icon ">
                                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                          <use class="" href="#icon-select"></use>
                                                        </svg>
                                                      </div>
                                                    </div>
                                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                                    <p class="help-box small text-muted">Send alert if this field's value changes</p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">value_changed</span>
                                              </div>
                                              <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="sender">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Sender</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input">
                                                      <div class="link-field ui-front" style="position: relative;">
                                                        <div class="awesomplete">
                                                          <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="sender" placeholder="" data-doctype="Notification" data-target="Email Account" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_8" role="combobox">
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
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">sender</span>
                                              </div>
                                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="send_system_notification">
                                                <div class="checkbox">
                                                  <label>
                                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="send_system_notification" placeholder="" data-doctype="Notification"></span>
                                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                  <span class="label-area">Send System Notification</span>
                                                  <span class="ml-1 help"></span>
                                                  </label>
                                                  <p class="help-box small text-muted">If enabled, the notification will show up in the notifications dropdown on the top right corner of the navigation bar.</p>
                                                </div>
                                                <span class="tooltip-content">send_system_notification</span>
                                              </div>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="sender_email">
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
                                                <span class="tooltip-content">sender_email</span>
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row form-section card-section visible-section" data-fieldname="section_break_9">
                                        <div class="section-body">
                                          <div class="form-column col-sm-6" data-fieldname="__column_3">
                                            <form>
                                              <div class="frappe-control" data-fieldtype="Code" data-fieldname="condition">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Condition</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input">
                                                      <div class="ace-editor-target border rounded ace_editor ace_hidpi ace-tomorrow" style="height: 300px;">
                                                        <textarea class="ace_text-input" wrap="off" autocorrect="off" autocapitalize="off" spellcheck="false" aria-haspopup="false" aria-autocomplete="both" role="textbox" style="opacity: 0; font-size: 1px; height: 1px; width: 1px; transform: translate(45px, 16px);"></textarea>
                                                        <div class="ace_gutter" aria-hidden="true" style="left: 0px; width: 41px;">
                                                          <div class="ace_layer ace_gutter-layer ace_folding-enabled" style="height: 1e+06px; transform: translate(0px, 0px); width: 41px;">
                                                            <div class="ace_gutter-cell ace_gutter-active-line " aria-hidden="true" style="height: 16px; top: 0px;">1<span tabindex="0" style="display: none;"></span><span tabindex="0" style="display: none;"><span></span></span></div>
                                                          </div>
                                                        </div>
                                                        <div class="ace_scroller " style="line-height: 16px; left: 41px; right: 0px; bottom: 0px;">
                                                          <div class="ace_content" style="transform: translate(0px, 0px); width: 551px; height: 330px;">
                                                            <div class="ace_layer ace_print-margin-layer">
                                                              <div class="ace_print-margin" style="left: 602px; visibility: hidden;"></div>
                                                            </div>
                                                            <div class="ace_layer ace_marker-layer">
                                                              <div class="ace_active-line" style="height: 16px; top: 0px; left: 0px; right: 0px;"></div>
                                                            </div>
                                                            <div class="ace_layer ace_text-layer" style="height: 1e+06px; margin: 0px 4px; transform: translate(0px, 0px);">
                                                              <div class="ace_line" style="height: 16px; top: 0px;"></div>
                                                            </div>
                                                            <div class="ace_layer ace_marker-layer"></div>
                                                            <div class="ace_layer ace_cursor-layer ace_hidden-cursors">
                                                              <div class="ace_cursor" style="display: block; transform: translate(4px, 0px); width: 7px; height: 16px;"></div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                        <div class="ace_scrollbar ace_scrollbar-v" style="display: none; width: 20px; height: 298px; bottom: 0px;">
                                                          <div class="ace_scrollbar-inner" style="width: 20px; height: 16px;">&nbsp;</div>
                                                        </div>
                                                        <div class="ace_scrollbar ace_scrollbar-h" style="display: none; height: 20px; left: 41px; right: 0px; width: 551px;">
                                                          <div class="ace_scrollbar-inner" style="height: 20px; width: 551px;">&nbsp;</div>
                                                        </div>
                                                        <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: hidden;">
                                                          <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;">הההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההה</div>
                                                          <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font-style: inherit; font-variant: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; font-optical-sizing: inherit; font-size-adjust: inherit; font-kerning: inherit; font-feature-settings: inherit; font-variation-settings: inherit; overflow: visible;">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                    <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                                      <pre></pre>
                                                    </div>
                                                    <p class="help-box small text-muted">Optional: The alert will be sent if this expression is true</p>
                                                    <button class="btn btn-xs btn-default">Expand</button>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">condition</span>
                                              </div>
                                            </form>
                                          </div>
                                          <div class="form-column col-sm-6" data-fieldname="column_break_6">
                                            <form>
                                              <div class="frappe-control" data-fieldtype="HTML" data-fieldname="html_7">
                                                <p><strong>Condition Examples:</strong></p>
                                                <pre>doc.status=="Open"<br>doc.due_date==nowdate()<br>doc.total &gt; 40000 </pre>
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row form-section card-section visible-section" data-fieldname="property_section">
                                        <div class="section-head collapsible collapsed">
                                          Set Property After Alert
                                          <span class="ml-2 collapse-indicator mb-1" style="">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="mb-1" href="#es-line-down"></use>
                                            </svg>
                                          </span>
                                        </div>
                                        <div class="section-body hide">
                                          <div class="form-column col-sm-12" data-fieldname="__column_4">
                                            <form>
                                              <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="set_property_after_alert">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Set Property After Alert</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input flex align-center">
                                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="set_property_after_alert" placeholder="" data-doctype="Notification"></select>
                                                      <div class="select-icon ">
                                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                          <use class="" href="#icon-select"></use>
                                                        </svg>
                                                      </div>
                                                    </div>
                                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">set_property_after_alert</span>
                                              </div>
                                              <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="property_value">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Value To Be Set</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="property_value" placeholder="" data-doctype="Notification"></div>
                                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">property_value</span>
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row form-section card-section visible-section" data-fieldname="column_break_5">
                                        <div class="section-head">
                                          Recipients
                                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                        </div>
                                        <div class="section-body">
                                          <div class="form-column col-sm-12" data-fieldname="__column_5">
                                            <form>
                                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="send_to_all_assignees">
                                                <div class="checkbox">
                                                  <label>
                                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="send_to_all_assignees" placeholder="" data-doctype="Notification"></span>
                                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                  <span class="label-area">Send To All Assignees</span>
                                                  <span class="ml-1 help"></span>
                                                  </label>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                                <span class="tooltip-content">send_to_all_assignees</span>
                                              </div>
                                              <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="recipients">
                                                <span class="tooltip-content">recipients</span>
                                                <div class="grid-field">
                                                  <label class="control-label">Recipients</label>
                                                  <span class="help"></span>
                                                  <p class="text-muted small grid-description" style="display: none;"></p>
                                                  <div class="grid-custom-buttons"></div>
                                                  <div class="form-grid-container">
                                                    <div class="form-grid error">
                                                      <div class="grid-heading-row">
                                                        <div class="grid-row">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>No.</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-4 " data-fieldname="receiver_by_document_field" data-fieldtype="Select" title="Receiver By Document Field">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis">Receiver By Document Field</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 " data-fieldname="receiver_by_role" data-fieldtype="Link" title="Receiver By Role">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis">Receiver By Role</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 " data-fieldname="condition" data-fieldtype="Data" title="Condition">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis">Condition</div>
                                                            </div>
                                                            <div class="col grid-static-col d-flex justify-content-center" style="cursor: pointer;">
                                                              <a>
                                                                <svg class="icon  icon-sm" style="filter: opacity(0.5)" aria-hidden="true">
                                                                  <use class="" href="#icon-setting-gear"></use>
                                                                </svg>
                                                              </a>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
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
                                                        <button type="button" class="grid-add-multiple-rows btn btn-xs btn-secondary hidden" style="display: inline-block;">
                                                        Add Multiple
                                                        </button>
                                                      </div>
                                                      <div class="grid-pagination"></div>
                                                      <div class="grid-bulk-actions text-right">
                                                        <button type="button" class="grid-download btn btn-xs btn-secondary hidden">
                                                        Download
                                                        </button>
                                                        <button type="button" class="grid-upload btn btn-xs btn-secondary hidden" style="display: inline-block;">
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
                                      <div class="row form-section card-section visible-section" data-fieldname="message_sb">
                                        <div class="section-head">
                                          Message
                                          <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                        </div>
                                        <div class="section-body">
                                          <div class="form-column col-sm-12" data-fieldname="__column_6">
                                            <form>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Select" data-fieldname="message_type">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Message Type</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input flex align-center">
                                                      <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="message_type" placeholder="" data-doctype="Notification">
                                                        <option value="Markdown">Markdown</option>
                                                        <option value="HTML">HTML</option>
                                                        <option value="Plain Text">Plain Text</option>
                                                      </select>
                                                      <div class="select-icon ">
                                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                          <use class="" href="#icon-select"></use>
                                                        </svg>
                                                      </div>
                                                    </div>
                                                    <div class="control-value like-disabled-input" style="display: none;">Markdown</div>
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">message_type</span>
                                              </div>
                                              <div class="frappe-control" data-fieldtype="Code" data-fieldname="message">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Message</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input">
                                                      <div class="ace-editor-target border rounded ace_editor ace_hidpi ace-tomorrow" style="height: 300px;">
                                                        <textarea class="ace_text-input" wrap="off" autocorrect="off" autocapitalize="off" spellcheck="false" aria-haspopup="false" aria-autocomplete="both" role="textbox" style="opacity: 0; font-size: 1px; height: 1px; width: 1px; transform: translate(45px, 16px);"></textarea>
                                                        <div class="ace_gutter" aria-hidden="true" style="left: 0px; width: 41px;">
                                                          <div class="ace_layer ace_gutter-layer ace_folding-enabled" style="height: 1e+06px; transform: translate(0px, 0px); width: 41px;">
                                                            <div class="ace_gutter-cell ace_gutter-active-line " aria-hidden="true" style="height: 16px; top: 0px;">1<span tabindex="0" style="display: none;"></span><span tabindex="0" style="display: none;"><span></span></span></div>
                                                          </div>
                                                        </div>
                                                        <div class="ace_scroller " style="line-height: 16px; left: 41px; right: 0px; bottom: 0px;">
                                                          <div class="ace_content" style="transform: translate(0px, 0px); width: 1175px; height: 330px;">
                                                            <div class="ace_layer ace_print-margin-layer">
                                                              <div class="ace_print-margin" style="left: 602px; visibility: hidden;"></div>
                                                            </div>
                                                            <div class="ace_layer ace_marker-layer">
                                                              <div class="ace_active-line" style="height: 16px; top: 0px; left: 0px; right: 0px;"></div>
                                                            </div>
                                                            <div class="ace_layer ace_text-layer" style="height: 1e+06px; margin: 0px 4px; transform: translate(0px, 0px);">
                                                              <div class="ace_line" style="height: 16px; top: 0px;"><span class="ace_text ace_xml">Add your message here</span></div>
                                                            </div>
                                                            <div class="ace_layer ace_marker-layer"></div>
                                                            <div class="ace_layer ace_cursor-layer ace_hidden-cursors">
                                                              <div class="ace_cursor" style="display: block; transform: translate(4px, 0px); width: 7px; height: 16px;"></div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                        <div class="ace_scrollbar ace_scrollbar-v" style="display: none; width: 20px; height: 298px; bottom: 0px;">
                                                          <div class="ace_scrollbar-inner" style="width: 20px; height: 16px;">&nbsp;</div>
                                                        </div>
                                                        <div class="ace_scrollbar ace_scrollbar-h" style="display: none; height: 20px; left: 41px; right: 0px; width: 1175px;">
                                                          <div class="ace_scrollbar-inner" style="height: 20px; width: 1175px;">&nbsp;</div>
                                                        </div>
                                                        <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: hidden;">
                                                          <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;">הההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההה</div>
                                                          <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font-style: inherit; font-variant: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; font-optical-sizing: inherit; font-size-adjust: inherit; font-kerning: inherit; font-feature-settings: inherit; font-variation-settings: inherit; overflow: visible;">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                    <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                                      <pre>Add your message here</pre>
                                                    </div>
                                                    <p class="help-box small text-muted"></p>
                                                    <button class="btn btn-xs btn-default">Expand</button>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">message</span>
                                              </div>
                                              <div class="frappe-control" data-fieldtype="HTML" data-fieldname="message_examples">
                                                <h5>Message Example</h5>
                                                <pre>&lt;h3&gt;Order Overdue&lt;/h3&gt;

                  &lt;p&gt;Transaction {{ doc.name }} has exceeded Due Date. Please take necessary action.&lt;/p&gt;

                  &lt;!-- show last comment --&gt;
                  {% if comments %}
                  Last comment: {{ comments[-1].comment }} by {{ comments[-1].by }}
                  {% endif %}

                  &lt;h4&gt;Details&lt;/h4&gt;

                  &lt;ul&gt;
                  &lt;li&gt;Customer: {{ doc.customer }}
                  &lt;li&gt;Amount: {{ doc.grand_total }}
                  &lt;/ul&gt;
                  </pre>
                                              </div>
                                              <div class="frappe-control input-max-width" data-fieldtype="Button" data-fieldname="view_properties">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label hide" style="padding-right: 0px;">&nbsp;</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input"><button class="btn btn-xs btn-default" data-fieldtype="Button" data-fieldname="view_properties" placeholder="" data-doctype="Notification" value="">View Properties (via Customize Form)</button></div>
                                                    <div class="hide" style="display: none;"></div>
                                                    <p class="help-box small text-muted"></p>
                                                  </div>
                                                </div>
                                                <span class="tooltip-content">view_properties</span>
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="row form-section card-section visible-section" data-fieldname="column_break_25">
                                        <div class="section-head collapsible collapsed">
                                          Print Settings
                                          <span class="ml-2 collapse-indicator mb-1" style="">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="mb-1" href="#es-line-down"></use>
                                            </svg>
                                          </span>
                                        </div>
                                        <div class="section-body hide">
                                          <div class="form-column col-sm-12" data-fieldname="__column_7">
                                            <form>
                                              <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="attach_print">
                                                <div class="checkbox">
                                                  <label>
                                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="attach_print" placeholder="" data-doctype="Notification"></span>
                                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                  <span class="label-area">Attach Print</span>
                                                  <span class="ml-1 help"></span>
                                                  </label>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                                <span class="tooltip-content">attach_print</span>
                                              </div>
                                              <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="print_format">
                                                <div class="form-group">
                                                  <div class="clearfix">
                                                    <label class="control-label" style="padding-right: 0px;">Print Format</label>
                                                    <span class="help"></span>
                                                  </div>
                                                  <div class="control-input-wrapper">
                                                    <div class="control-input">
                                                      <div class="link-field ui-front" style="position: relative;">
                                                        <div class="awesomplete">
                                                          <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="print_format" placeholder="" data-doctype="Notification" data-target="Print Format" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_9" role="combobox">
                                                          <ul hidden="" role="listbox" id="awesomplete_list_9"></ul>
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
                                                <span class="tooltip-content">print_format</span>
                                              </div>
                                            </form>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="layout-footer hide"></div>
                            <div class="hide">
                              <div class="form-footer">
                                <div class="after-save">
                                  <div class="comment-box">
                                    <div class="comment-input-wrapper">
                                      <div class="comment-input-header">
                                        <span>Comments</span>
                                      </div>
                                      <div class="comment-input-container">
                                        <span class="avatar avatar-medium" title="Sai">
                                          <div class="avatar-frame standard-image" style="background-color: var(--blue-avatar-bg); color: var(--blue-avatar-color)" title="Sai">
                                            S
                                          </div>
                                        </span>
                                        <div class="frappe-control col" data-fieldtype="Comment" data-fieldname="comment">
                                          <span class="tooltip-content">comment</span>
                                          <div class="ql-container ql-bubble" style="position: relative;">
                                            <div class="ql-editor ql-blank" data-gramm="false" contenteditable="true" data-placeholder="Type a reply / comment">
                                              <p><br></p>
                                            </div>
                                            <div class="ql-tooltip ql-hidden">
                                              <span class="ql-tooltip-arrow"></span>
                                              <div class="ql-tooltip-editor"><input type="text" data-formula="e=mc^2" data-link="https://quilljs.com" data-video="Embed URL"><a class="ql-close"></a></div>
                                              <div class="ql-toolbar">
                                                <span class="ql-formats">
                                                  <button type="button" class="ql-bold">
                                                    <svg viewBox="0 0 18 18">
                                                      <path class="ql-stroke" d="M5,4H9.5A2.5,2.5,0,0,1,12,6.5v0A2.5,2.5,0,0,1,9.5,9H5A0,0,0,0,1,5,9V4A0,0,0,0,1,5,4Z"></path>
                                                      <path class="ql-stroke" d="M5,9h5.5A2.5,2.5,0,0,1,13,11.5v0A2.5,2.5,0,0,1,10.5,14H5a0,0,0,0,1,0,0V9A0,0,0,0,1,5,9Z"></path>
                                                    </svg>
                                                  </button>
                                                  <button type="button" class="ql-italic">
                                                    <svg viewBox="0 0 18 18">
                                                      <line class="ql-stroke" x1="7" x2="13" y1="4" y2="4"></line>
                                                      <line class="ql-stroke" x1="5" x2="11" y1="14" y2="14"></line>
                                                      <line class="ql-stroke" x1="8" x2="10" y1="14" y2="4"></line>
                                                    </svg>
                                                  </button>
                                                  <button type="button" class="ql-underline">
                                                    <svg viewBox="0 0 18 18">
                                                      <path class="ql-stroke" d="M5,3V9a4.012,4.012,0,0,0,4,4H9a4.012,4.012,0,0,0,4-4V3"></path>
                                                      <rect class="ql-fill" height="1" rx="0.5" ry="0.5" width="12" x="3" y="15"></rect>
                                                    </svg>
                                                  </button>
                                                  <button type="button" class="ql-strike">
                                                    <svg viewBox="0 0 18 18">
                                                      <line class="ql-stroke ql-thin" x1="15.5" x2="2.5" y1="8.5" y2="9.5"></line>
                                                      <path class="ql-fill" d="M9.007,8C6.542,7.791,6,7.519,6,6.5,6,5.792,7.283,5,9,5c1.571,0,2.765.679,2.969,1.309a1,1,0,0,0,1.9-.617C13.356,4.106,11.354,3,9,3,6.2,3,4,4.538,4,6.5a3.2,3.2,0,0,0,.5,1.843Z"></path>
                                                      <path class="ql-fill" d="M8.984,10C11.457,10.208,12,10.479,12,11.5c0,0.708-1.283,1.5-3,1.5-1.571,0-2.765-.679-2.969-1.309a1,1,0,1,0-1.9.617C4.644,13.894,6.646,15,9,15c2.8,0,5-1.538,5-3.5a3.2,3.2,0,0,0-.5-1.843Z"></path>
                                                    </svg>
                                                  </button>
                                                </span>
                                                <span class="ql-formats">
                                                  <button type="button" class="ql-blockquote">
                                                    <svg viewBox="0 0 18 18">
                                                      <rect class="ql-fill ql-stroke" height="3" width="3" x="4" y="5"></rect>
                                                      <rect class="ql-fill ql-stroke" height="3" width="3" x="11" y="5"></rect>
                                                      <path class="ql-even ql-fill ql-stroke" d="M7,8c0,4.031-3,5-3,5"></path>
                                                      <path class="ql-even ql-fill ql-stroke" d="M14,8c0,4.031-3,5-3,5"></path>
                                                    </svg>
                                                  </button>
                                                  <button type="button" class="ql-code-block">
                                                    <svg viewBox="0 0 18 18">
                                                      <polyline class="ql-even ql-stroke" points="5 7 3 9 5 11"></polyline>
                                                      <polyline class="ql-even ql-stroke" points="13 7 15 9 13 11"></polyline>
                                                      <line class="ql-stroke" x1="10" x2="8" y1="5" y2="13"></line>
                                                    </svg>
                                                  </button>
                                                </span>
                                                <span class="ql-formats">
                                                  <button type="button" class="ql-direction" value="rtl">
                                                    <svg viewBox="0 0 18 18">
                                                      <polygon class="ql-stroke ql-fill" points="3 11 5 9 3 7 3 11"></polygon>
                                                      <line class="ql-stroke ql-fill" x1="15" x2="11" y1="4" y2="4"></line>
                                                      <path class="ql-fill" d="M11,3a3,3,0,0,0,0,6h1V3H11Z"></path>
                                                      <rect class="ql-fill" height="11" width="1" x="11" y="4"></rect>
                                                      <rect class="ql-fill" height="11" width="1" x="13" y="4"></rect>
                                                    </svg>
                                                    <svg viewBox="0 0 18 18">
                                                      <polygon class="ql-stroke ql-fill" points="15 12 13 10 15 8 15 12"></polygon>
                                                      <line class="ql-stroke ql-fill" x1="9" x2="5" y1="4" y2="4"></line>
                                                      <path class="ql-fill" d="M5,3A3,3,0,0,0,5,9H6V3H5Z"></path>
                                                      <rect class="ql-fill" height="11" width="1" x="5" y="4"></rect>
                                                      <rect class="ql-fill" height="11" width="1" x="7" y="4"></rect>
                                                    </svg>
                                                  </button>
                                                </span>
                                                <span class="ql-formats">
                                                  <button type="button" class="ql-link">
                                                    <svg viewBox="0 0 18 18">
                                                      <line class="ql-stroke" x1="7" x2="11" y1="7" y2="11"></line>
                                                      <path class="ql-even ql-stroke" d="M8.9,4.577a3.476,3.476,0,0,1,.36,4.679A3.476,3.476,0,0,1,4.577,8.9C3.185,7.5,2.035,6.4,4.217,4.217S7.5,3.185,8.9,4.577Z"></path>
                                                      <path class="ql-even ql-stroke" d="M13.423,9.1a3.476,3.476,0,0,0-4.679-.36,3.476,3.476,0,0,0,.36,4.679c1.392,1.392,2.5,2.542,4.679.36S14.815,10.5,13.423,9.1Z"></path>
                                                    </svg>
                                                  </button>
                                                  <button type="button" class="ql-image">
                                                    <svg viewBox="0 0 18 18">
                                                      <rect class="ql-stroke" height="10" width="12" x="3" y="4"></rect>
                                                      <circle class="ql-fill" cx="6" cy="7" r="1"></circle>
                                                      <polyline class="ql-even ql-fill" points="5 12 5 11 7 9 8 10 11 7 13 9 13 12 5 12"></polyline>
                                                    </svg>
                                                  </button>
                                                </span>
                                                <span class="ql-formats">
                                                  <button type="button" class="ql-list" value="ordered">
                                                    <svg viewBox="0 0 18 18">
                                                      <line class="ql-stroke" x1="7" x2="15" y1="4" y2="4"></line>
                                                      <line class="ql-stroke" x1="7" x2="15" y1="9" y2="9"></line>
                                                      <line class="ql-stroke" x1="7" x2="15" y1="14" y2="14"></line>
                                                      <line class="ql-stroke ql-thin" x1="2.5" x2="4.5" y1="5.5" y2="5.5"></line>
                                                      <path class="ql-fill" d="M3.5,6A0.5,0.5,0,0,1,3,5.5V3.085l-0.276.138A0.5,0.5,0,0,1,2.053,3c-0.124-.247-0.023-0.324.224-0.447l1-.5A0.5,0.5,0,0,1,4,2.5v3A0.5,0.5,0,0,1,3.5,6Z"></path>
                                                      <path class="ql-stroke ql-thin" d="M4.5,10.5h-2c0-.234,1.85-1.076,1.85-2.234A0.959,0.959,0,0,0,2.5,8.156"></path>
                                                      <path class="ql-stroke ql-thin" d="M2.5,14.846a0.959,0.959,0,0,0,1.85-.109A0.7,0.7,0,0,0,3.75,14a0.688,0.688,0,0,0,.6-0.736,0.959,0.959,0,0,0-1.85-.109"></path>
                                                    </svg>
                                                  </button>
                                                  <button type="button" class="ql-list" value="bullet">
                                                    <svg viewBox="0 0 18 18">
                                                      <line class="ql-stroke" x1="6" x2="15" y1="4" y2="4"></line>
                                                      <line class="ql-stroke" x1="6" x2="15" y1="9" y2="9"></line>
                                                      <line class="ql-stroke" x1="6" x2="15" y1="14" y2="14"></line>
                                                      <line class="ql-stroke" x1="3" x2="3" y1="4" y2="4"></line>
                                                      <line class="ql-stroke" x1="3" x2="3" y1="9" y2="9"></line>
                                                      <line class="ql-stroke" x1="3" x2="3" y1="14" y2="14"></line>
                                                    </svg>
                                                  </button>
                                                </span>
                                                <span class="ql-formats">
                                                  <span class="ql-align ql-picker ql-icon-picker">
                                                    <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-0">
                                                      <svg viewBox="0 0 18 18">
                                                        <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                        <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                        <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                      </svg>
                                                    </span>
                                                    <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-0">
                                                      <span tabindex="0" role="button" class="ql-picker-item ql-selected">
                                                        <svg viewBox="0 0 18 18">
                                                          <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                          <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                          <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                        </svg>
                                                      </span>
                                                      <span tabindex="0" role="button" class="ql-picker-item" data-value="center">
                                                        <svg viewBox="0 0 18 18">
                                                          <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                          <line class="ql-stroke" x1="14" x2="4" y1="14" y2="14"></line>
                                                          <line class="ql-stroke" x1="12" x2="6" y1="4" y2="4"></line>
                                                        </svg>
                                                      </span>
                                                      <span tabindex="0" role="button" class="ql-picker-item" data-value="right">
                                                        <svg viewBox="0 0 18 18">
                                                          <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                          <line class="ql-stroke" x1="15" x2="5" y1="14" y2="14"></line>
                                                          <line class="ql-stroke" x1="15" x2="9" y1="4" y2="4"></line>
                                                        </svg>
                                                      </span>
                                                      <span tabindex="0" role="button" class="ql-picker-item" data-value="justify">
                                                        <svg viewBox="0 0 18 18">
                                                          <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                          <line class="ql-stroke" x1="15" x2="3" y1="14" y2="14"></line>
                                                          <line class="ql-stroke" x1="15" x2="3" y1="4" y2="4"></line>
                                                        </svg>
                                                      </span>
                                                    </span>
                                                  </span>
                                                  <select class="ql-align" style="display: none;">
                                                    <option selected="selected"></option>
                                                    <option value="center"></option>
                                                    <option value="right"></option>
                                                    <option value="justify"></option>
                                                  </select>
                                                </span>
                                                <span class="ql-formats">
                                                  <button type="button" class="ql-clean">
                                                    <svg class="" viewBox="0 0 18 18">
                                                      <line class="ql-stroke" x1="5" x2="13" y1="3" y2="3"></line>
                                                      <line class="ql-stroke" x1="6" x2="9.35" y1="12" y2="3"></line>
                                                      <line class="ql-stroke" x1="11" x2="15" y1="11" y2="15"></line>
                                                      <line class="ql-stroke" x1="15" x2="11" y1="11" y2="15"></line>
                                                      <rect class="ql-fill" height="1" rx="0.5" ry="0.5" width="7" x="2" y="14"></rect>
                                                    </svg>
                                                  </button>
                                                </span>
                                              </div>
                                            </div>
                                            <div class="ql-mention-list-container" style="display: none; position: absolute;">
                                              <ul class="ql-mention-list"></ul>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <button class="btn hidden btn-comment btn-xs" style="margin-left:48px;">
                                      Comment
                                      </button>
                                    </div>
                                  </div>
                                  <div class="new-timeline">
                                    <div class="timeline-item activity-title">
                                      <h4>Activity</h4>
                                      <div class="timeline-items timeline-actions" style="display: none;">
                                        <div class="timeline-item">
                                          <div class="timeline-content action-buttons"></div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="timeline-items">
                                      <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-content ">You created this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
                                      </div>
                                      <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-content "><a href="/app/user/undefined">Sai</a> last edited this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <button class="scroll-to-top btn btn-default icon-btn" onclick="frappe.utils.scroll_to(0)">
                                  <svg class="icon icon-xs">
                                    <use href="#icon-up-line"></use>
                                  </svg>
                                </button>
                              </div>
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