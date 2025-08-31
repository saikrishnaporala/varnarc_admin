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
    <Head title="Notification Settings" />
    <PageHeader title="Notification Settings" pageTitle="Emails/Notifications" subTitle="Notification"/>
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12" class="mt-3">
                  <div class="content page-container" id="page-List/Notification Settings/List" data-page-route="List/Notification Settings/List" style="">
                    <div class="page-head flex">
                      <div class="container">
                        <div class="row flex align-center page-head-content justify-between">
                          <div class="col-md-4 col-sm-6 col-xs-7 page-title">
                            <!-- <div class="title-image hide hidden-md hidden-lg"></div> -->     <!-- title -->     
                            <button class="btn-reset sidebar-toggle-btn" title="" aria-label="Toggle Sidebar" data-original-title="Toggle Sidebar">
                              <svg class="es-icon icon-md sidebar-toggle-placeholder">
                                <use href="#es-line-align-justify"></use>
                              </svg>
                              <span class="sidebar-toggle-icon">
                                <svg class="es-icon icon-md">
                                  <use href="#es-line-sidebar-collapse">        </use>
                                </svg>
                              </span>
                            </button>
                            <div class="flex fill-width title-area">
                              <div>
                                <div class="flex">
                                  <h3 class="ellipsis title-text" title="Notification Settings">Notification Settings</h3>
                                  <span class="indicator-pill whitespace-nowrap"></span>       
                                </div>
                                <div class="ellipsis sub-heading hide text-muted"></div>
                              </div>
                              <button class="btn btn-default more-button hide">
                                <svg class="icon icon-sm">
                                  <use href="#icon-dot-horizontal">        </use>
                                </svg>
                              </button>
                            </div>
                          </div>
                          <div class="flex col page-actions justify-content-end">
                            <!-- buttons -->     
                            <div class="custom-actions hidden-xs hidden-md">
                              <div class="custom-btn-group">
                                <button type="button" class="btn btn-default btn-sm ellipsis" data-toggle="dropdown" aria-expanded="false">
                                  <span class="hidden-xs">
                                    <svg class="icon  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#icon-list"></use>
                                    </svg>
                                    <span class="custom-btn-group-label">List View</span>
                                    <svg class="icon  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#icon-select"></use>
                                    </svg>
                                  </span>
                                  <span class="visible-xs">
                                    <svg class="icon  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#icon-list"></use>
                                    </svg>
                                  </span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                  <li data-view="Report">
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                      <span class="menu-item-icon">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-small-file"></use>
                                        </svg>
                                      </span>
                                      <span class="menu-item-label" data-label="Report%20View"><span><span class="alt-underline">R</span>eport View</span></span>
                                    </a>
                                  </li>
                                  <li data-view="Dashboard">
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                      <span class="menu-item-icon">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-dashboard"></use>
                                        </svg>
                                      </span>
                                      <span class="menu-item-label" data-label="Dashboard%20View"><span><span class="alt-underline">D</span>ashboard View</span></span>
                                    </a>
                                  </li>
                                  <li data-view="Kanban">
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                      <span class="menu-item-icon">
                                        <svg class="icon  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#icon-kanban"></use>
                                        </svg>
                                      </span>
                                      <span class="menu-item-label" data-label="Kanban%20View"><span><span class="alt-underline">K</span>anban View</span></span>
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div class="standard-actions flex">
                              <span class="page-icon-group hidden-xs hidden-sm">
                                <button class="text-muted btn btn-default  icon-btn" title="" data-original-title="Reload List">
                                  <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                    <use class="" href="#es-line-reload"></use>
                                  </svg>
                                </button>
                              </span>
                              <div class="menu-btn-group">
                                <button type="button" class="btn btn-default icon-btn" data-toggle="dropdown" aria-expanded="false" aria-label="Menu" title="" data-original-title="Menu">
                                  <span>
                                    <span class="menu-btn-group-label" data-label="">
                                      <svg class="icon icon-sm">
                                        <use href="#icon-dot-horizontal">           </use>
                                      </svg>
                                    </span>
                                  </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li class="user-action">
                                    <a class="grey-link dropdown-item visible-xs" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Refresh"><span><span class="alt-underline">R</span>efresh</span></span>
                                    </a>
                                  </li>
                                  <li class="dropdown-divider user-action"></li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="User%20Permissions"><span><span class="alt-underline">U</span>ser Permissions</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Role%20Permissions%20Manager"><span>R<span class="alt-underline">o</span>le Permissions Manager</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Customize"><span><span class="alt-underline">C</span>ustomize</span></span>
                                    <kbd class="pull-right">
                                    <span>⌘+J</span>
                                    </kbd>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Toggle%20Sidebar"><span><span class="alt-underline">T</span>oggle Sidebar</span></span>
                                    <kbd class="pull-right">
                                    <span>⌘+K</span>
                                    </kbd>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="List%20Settings"><span><span class="alt-underline">L</span>ist Settings</span></span>
                                    </a>
                                  </li>
                                </ul>
                              </div>
                              <button class="btn btn-secondary btn-default btn-sm hide"></button>      
                              <div class="actions-btn-group hide">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="dropdown" aria-expanded="false">
                                  <span>
                                    <span class="hidden-xs actions-btn-group-label" data-label="Actions"><span><span class="alt-underline">A</span>ctions</span></span>         
                                    <svg class="icon icon-xs">
                                      <use href="#icon-select">          </use>
                                    </svg>
                                  </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Edit"><span><span class="alt-underline">E</span>dit</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Export"><span>E<span class="alt-underline">x</span>port</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Assign%20To"><span><span class="alt-underline">A</span>ssign To</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Clear%20Assignment"><span><span class="alt-underline">C</span>lear Assignment</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Apply%20Assignment%20Rule"><span>A<span class="alt-underline">p</span>ply Assignment Rule</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Add%20Tags"><span>A<span class="alt-underline">d</span>d Tags</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Print"><span>P<span class="alt-underline">r</span>int</span></span>
                                    </a>
                                  </li>
                                </ul>
                              </div>
                              <button class="btn btn-primary btn-sm hide primary-action"></button>     
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
                                            <li>      <a class="dropdown-item option" data-value="name">       ID      </a>     </li>
                                            <li>      <a class="dropdown-item option" data-value="creation">       Created On      </a>     </li>
                                            <li>      <a class="dropdown-item option" data-value="idx">       Most Used      </a>     </li>
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
                                          <span class="level-item" data-sort-by="name" title="Click to sort by ID">
                                          ID
                                          </span>
                                        </div>
                                        <div class="list-row-col ellipsis hidden-xs tag-col hide "><span>Tag</span></div>
                                        <div class="list-row-col ellipsis hidden-xs  "><span>Status</span></div>
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
                                            <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Notification Settings" data-name="saikrishnaporala@gmail.com"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Notification Settings" data-name="saikrishnaporala@gmail.com" href="/app/notification-settings/saikrishnaporala%40gmail.com" title="saikrishnaporala@gmail.com">saikrishnaporala@gmail.com</a></span>
                                          </div>
                                          <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                            <div class="tags-empty">-</div>
                                          </div>
                                          <div class="list-row-col hidden-xs ellipsis">
                                            <span class="indicator-pill blue filterable no-indicator-dot ellipsis" data-filter="enabled,=,1" title="Document is in draft state">
                                            <span class="ellipsis"> Enabled</span>
                                            </span>
                                          </div>
                                        </div>
                                        <div class="level-right text-muted ellipsis">
                                          <div class="level-item list-row-activity hidden-xs">
                                            <div class="hidden-md hidden-xs">
                                            </div>
                                            <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 13:59:12.020972" title="24-11-2024 13:59:12">8 h</span></span>
                                            <span class="comment-count d-flex align-items-center">
                                              <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                <use class="" href="#es-line-chat-alt"></use>
                                              </svg>
                                              0
                                            </span>
                                            <span class="mx-2">·</span>
                                            <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                              <span class="like-action not-liked" data-doctype="Notification Settings" data-name="saikrishnaporala@gmail.com" data-liked-by="" title="">
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
                                            <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Notification Settings" data-name="Guest"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Notification Settings" data-name="Guest" href="/app/notification-settings/Guest" title="Guest">Guest</a></span>
                                          </div>
                                          <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                            <div class="tags-empty">-</div>
                                          </div>
                                          <div class="list-row-col hidden-xs ellipsis">
                                            <span class="indicator-pill blue filterable no-indicator-dot ellipsis" data-filter="enabled,=,1" title="Document is in draft state">
                                            <span class="ellipsis"> Enabled</span>
                                            </span>
                                          </div>
                                        </div>
                                        <div class="level-right text-muted ellipsis">
                                          <div class="level-item list-row-activity hidden-xs">
                                            <div class="hidden-md hidden-xs">
                                            </div>
                                            <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-23 15:41:12.967773" title="23-11-2024 15:41:12">1 d</span></span>
                                            <span class="comment-count d-flex align-items-center">
                                              <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                <use class="" href="#es-line-chat-alt"></use>
                                              </svg>
                                              0
                                            </span>
                                            <span class="mx-2">·</span>
                                            <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                              <span class="like-action not-liked" data-doctype="Notification Settings" data-name="Guest" data-liked-by="" title="">
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
                                        <p>You haven't created a Notification Settings yet</p>
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