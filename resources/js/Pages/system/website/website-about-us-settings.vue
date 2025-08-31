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
    <Head title="Website Settings" />
    <PageHeader title="Website Settings" pageTitle="Website"/>
    <BRow class="project-wrapper">
      <BCol xxl="12">
        <BRow>
          <BCol xl="12">
            <BCard no-body>
              <BCardBody class="pt-0">
                <BCol xl="12" class="mt-3">
                  <div class="content page-container editable-form" id="page-About Us Settings" data-page-route="About Us Settings" data-state="clean" style="">
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
                                  <h3 class="ellipsis title-text" title="About Us Settings">About Us Settings</h3>
                                  <span class="indicator-pill no-indicator-dot whitespace-nowrap hide"></span>       
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
                            <div class="form-viewers d-flex" data-original-title="" title=""></div>
                            <!-- buttons -->     
                            <div class="custom-actions hide hidden-xs hidden-md"></div>
                            <div class="standard-actions flex">
                              <span class="page-icon-group hide hidden-xs hidden-sm"></span>      
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
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Email"><span><span class="alt-underline">E</span>mail</span></span>
                                    <kbd class="pull-right">
                                    <span>⌘+E</span>
                                    </kbd>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Jump%20to%20field"><span><span class="alt-underline">J</span>ump to field</span></span>
                                    <kbd class="pull-right">
                                    <span>⌘+J</span>
                                    </kbd>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Copy%20to%20Clipboard"><span><span class="alt-underline">C</span>opy to Clipboard</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Reload"><span><span class="alt-underline">R</span>eload</span></span>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Remind%20Me"><span>Re<span class="alt-underline">m</span>ind Me</span></span>
                                    <kbd class="pull-right">
                                    <span>⇧+R</span>
                                    </kbd>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Undo"><span><span class="alt-underline">U</span>ndo</span></span>
                                    <kbd class="pull-right">
                                    <span>⌘+Z</span>
                                    </kbd>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                    <span class="menu-item-label" data-label="Redo"><span>Re<span class="alt-underline">d</span>o</span></span>
                                    <kbd class="pull-right">
                                    <span>⌘+Y</span>
                                    </kbd>
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
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">       </ul>
                              </div>
                              <button class="btn btn-primary btn-sm primary-action" data-label="Save"><span>S<span class="alt-underline">a</span>ve</span></button>     
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
                            <div class="col-lg-2 layout-side-section">
                              <div class="form-sidebar overlay-sidebar hidden-xs hidden-sm">
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
                                <ul class="list-unstyled sidebar-menu form-assignments">
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
                                    <div class="assignments" style="display: none;"></div>
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
                                <ul class="list-unstyled sidebar-menu form-tags" style="display: none;">
                                  <li>
                                    <span class="form-sidebar-items">
                                      <span>
                                        <svg class="es-icon ml-0 icon-sm">
                                          <use href="#es-line-tag"></use>
                                        </svg>
                                        <span class="tags-label ellipsis">Tags</span>     
                                      </span>
                                    </span>
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
                                    <div class="shares" style="display: none;"></div>
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
                                        <span class="liked-by like-action d-flex align-items-center not-liked" data-doctype="About Us Settings" data-name="About Us Settings">
                                          <svg class="es-icon icon-sm">
                                            <use href="#es-solid-heart" class="like-icon"></use>
                                          </svg>
                                          <span class="like-count ml-2">0</span>     
                                        </span>
                                      </span>
                                      <span class="mx-2">·</span>    
                                      <a class="comments d-flex align-items-center">
                                        <svg class="es-icon icon-sm">
                                          <use href="#es-line-chat-alt" class="comment-icon"></use>
                                        </svg>
                                        <span class="comments-count ml-2">0</span>    
                                      </a>
                                    </div>
                                    <a class="form-follow text-sm">Follow</a>  
                                  </li>
                                </ul>
                                <hr>
                                <ul class="list-unstyled sidebar-menu text-muted">
                                  <li class="pageview-count"></li>
                                  <li class="modified-by"><a href="/app/user/Administrator">Administrator</a> last edited this · <span class="frappe-timestamp " data-timestamp="2024-11-23 15:41:12.024844" title="23-11-2024 15:41:12">yesterday</span></li>
                                  <li class="created-by"><a href="/app/user/Administrator">Administrator</a> created this · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></li>
                                </ul>
                              </div>
                            </div>
                            <div class="col layout-main-section-wrapper">
                              <div class="layout-main-section">
                                <div class="page-form row hide"></div>
                                <div>
                                  <div class="std-form-layout">
                                    <div class="form-layout">
                                      <div class="form-message blue">
                                        <div>Link for About Us Page is "/about".</div>
                                        <div class="close-message">
                                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                                            <use class="" href="#icon-close"></use>
                                          </svg>
                                        </div>
                                      </div>
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
                                              <div id="heatmap-about_us_settings" class="heatmap"></div>
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
                                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="page_title">
                                                  <div class="form-group">
                                                    <div class="clearfix">
                                                      <label class="control-label" style="padding-right: 0px;">Page Title</label>
                                                      <span class="help"></span>
                                                    </div>
                                                    <div class="control-input-wrapper">
                                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                                      <p class="help-box small text-muted"></p>
                                                    </div>
                                                  </div>
                                                  <span class="tooltip-content">page_title</span>
                                                </div>
                                                <div class="frappe-control" data-fieldtype="Text Editor" data-fieldname="company_introduction">
                                                  <div class="form-group">
                                                    <div class="clearfix">
                                                      <label class="control-label" style="padding-right: 0px;">Company Introduction</label>
                                                      <span class="help"></span>
                                                    </div>
                                                    <div class="control-input-wrapper">
                                                      <div class="control-input">
                                                        <div class="ql-toolbar ql-snow">
                                                          <span class="ql-formats">
                                                            <span class="ql-header ql-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-1">
                                                                <svg viewBox="0 0 18 18">
                                                                  <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                                  <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-1"><span tabindex="0" role="button" class="ql-picker-item" data-value="1"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="2"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="3"></span><span tabindex="0" role="button" class="ql-picker-item ql-selected"></span></span>
                                                            </span>
                                                            <select class="ql-header" style="display: none;">
                                                              <option value="1"></option>
                                                              <option value="2"></option>
                                                              <option value="3"></option>
                                                              <option selected="selected"></option>
                                                            </select>
                                                          </span>
                                                          <span class="ql-formats">
                                                            <span class="ql-size ql-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-2" data-value="---">
                                                                <svg viewBox="0 0 18 18">
                                                                  <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                                  <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-2"><span tabindex="0" role="button" class="ql-picker-item ql-selected" data-value="---"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="8px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="9px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="10px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="11px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="12px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="13px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="14px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="15px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="16px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="18px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="20px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="22px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="24px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="32px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="36px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="40px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="48px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="54px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="64px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="96px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="128px"></span></span>
                                                            </span>
                                                            <select class="ql-size" style="display: none;">
                                                              <option selected="selected"></option>
                                                              <option value="8px"></option>
                                                              <option value="9px"></option>
                                                              <option value="10px"></option>
                                                              <option value="11px"></option>
                                                              <option value="12px"></option>
                                                              <option value="13px"></option>
                                                              <option value="14px"></option>
                                                              <option value="15px"></option>
                                                              <option value="16px"></option>
                                                              <option value="18px"></option>
                                                              <option value="20px"></option>
                                                              <option value="22px"></option>
                                                              <option value="24px"></option>
                                                              <option value="32px"></option>
                                                              <option value="36px"></option>
                                                              <option value="40px"></option>
                                                              <option value="48px"></option>
                                                              <option value="54px"></option>
                                                              <option value="64px"></option>
                                                              <option value="96px"></option>
                                                              <option value="128px"></option>
                                                            </select>
                                                          </span>
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
                                                          <span class="ql-formats">
                                                            <span class="ql-color ql-picker ql-color-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-3">
                                                                <svg viewBox="0 0 18 18">
                                                                  <line class="ql-color-label ql-stroke ql-transparent" x1="3" x2="15" y1="15" y2="15"></line>
                                                                  <polyline class="ql-stroke" points="5.5 11 9 3 12.5 11"></polyline>
                                                                  <line class="ql-stroke" x1="11.63" x2="6.38" y1="9" y2="9"></line>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-3"><span tabindex="0" role="button" class="ql-picker-item ql-selected ql-primary"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#e60000" style="background-color: rgb(230, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ff9900" style="background-color: rgb(255, 153, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ffff00" style="background-color: rgb(255, 255, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#008a00" style="background-color: rgb(0, 138, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#0066cc" style="background-color: rgb(0, 102, 204);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#9933ff" style="background-color: rgb(153, 51, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffff" style="background-color: rgb(255, 255, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#facccc" style="background-color: rgb(250, 204, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffebcc" style="background-color: rgb(255, 235, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffcc" style="background-color: rgb(255, 255, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce8cc" style="background-color: rgb(204, 232, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce0f5" style="background-color: rgb(204, 224, 245);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ebd6ff" style="background-color: rgb(235, 214, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#bbbbbb" style="background-color: rgb(187, 187, 187);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#f06666" style="background-color: rgb(240, 102, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffc266" style="background-color: rgb(255, 194, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffff66" style="background-color: rgb(255, 255, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66b966" style="background-color: rgb(102, 185, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66a3e0" style="background-color: rgb(102, 163, 224);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#c285ff" style="background-color: rgb(194, 133, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#888888" style="background-color: rgb(136, 136, 136);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#a10000" style="background-color: rgb(161, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b26b00" style="background-color: rgb(178, 107, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b2b200" style="background-color: rgb(178, 178, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#006100" style="background-color: rgb(0, 97, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#0047b2" style="background-color: rgb(0, 71, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#6b24b2" style="background-color: rgb(107, 36, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#444444" style="background-color: rgb(68, 68, 68);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#5c0000" style="background-color: rgb(92, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#663d00" style="background-color: rgb(102, 61, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#666600" style="background-color: rgb(102, 102, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#003700" style="background-color: rgb(0, 55, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#002966" style="background-color: rgb(0, 41, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#3d1466" style="background-color: rgb(61, 20, 102);"></span></span>
                                                            </span>
                                                            <select class="ql-color" style="display: none;">
                                                              <option selected="selected"></option>
                                                              <option value="#e60000"></option>
                                                              <option value="#ff9900"></option>
                                                              <option value="#ffff00"></option>
                                                              <option value="#008a00"></option>
                                                              <option value="#0066cc"></option>
                                                              <option value="#9933ff"></option>
                                                              <option value="#ffffff"></option>
                                                              <option value="#facccc"></option>
                                                              <option value="#ffebcc"></option>
                                                              <option value="#ffffcc"></option>
                                                              <option value="#cce8cc"></option>
                                                              <option value="#cce0f5"></option>
                                                              <option value="#ebd6ff"></option>
                                                              <option value="#bbbbbb"></option>
                                                              <option value="#f06666"></option>
                                                              <option value="#ffc266"></option>
                                                              <option value="#ffff66"></option>
                                                              <option value="#66b966"></option>
                                                              <option value="#66a3e0"></option>
                                                              <option value="#c285ff"></option>
                                                              <option value="#888888"></option>
                                                              <option value="#a10000"></option>
                                                              <option value="#b26b00"></option>
                                                              <option value="#b2b200"></option>
                                                              <option value="#006100"></option>
                                                              <option value="#0047b2"></option>
                                                              <option value="#6b24b2"></option>
                                                              <option value="#444444"></option>
                                                              <option value="#5c0000"></option>
                                                              <option value="#663d00"></option>
                                                              <option value="#666600"></option>
                                                              <option value="#003700"></option>
                                                              <option value="#002966"></option>
                                                              <option value="#3d1466"></option>
                                                            </select>
                                                            <span class="ql-background ql-picker ql-color-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-4">
                                                                <svg viewBox="0 0 18 18">
                                                                  <g class="ql-fill ql-color-label">
                                                                    <polygon points="6 6.868 6 6 5 6 5 7 5.942 7 6 6.868"></polygon>
                                                                    <rect height="1" width="1" x="4" y="4"></rect>
                                                                    <polygon points="6.817 5 6 5 6 6 6.38 6 6.817 5"></polygon>
                                                                    <rect height="1" width="1" x="2" y="6"></rect>
                                                                    <rect height="1" width="1" x="3" y="5"></rect>
                                                                    <rect height="1" width="1" x="4" y="7"></rect>
                                                                    <polygon points="4 11.439 4 11 3 11 3 12 3.755 12 4 11.439"></polygon>
                                                                    <rect height="1" width="1" x="2" y="12"></rect>
                                                                    <rect height="1" width="1" x="2" y="9"></rect>
                                                                    <rect height="1" width="1" x="2" y="15"></rect>
                                                                    <polygon points="4.63 10 4 10 4 11 4.192 11 4.63 10"></polygon>
                                                                    <rect height="1" width="1" x="3" y="8"></rect>
                                                                    <path d="M10.832,4.2L11,4.582V4H10.708A1.948,1.948,0,0,1,10.832,4.2Z"></path>
                                                                    <path d="M7,4.582L7.168,4.2A1.929,1.929,0,0,1,7.292,4H7V4.582Z"></path>
                                                                    <path d="M8,13H7.683l-0.351.8a1.933,1.933,0,0,1-.124.2H8V13Z"></path>
                                                                    <rect height="1" width="1" x="12" y="2"></rect>
                                                                    <rect height="1" width="1" x="11" y="3"></rect>
                                                                    <path d="M9,3H8V3.282A1.985,1.985,0,0,1,9,3Z"></path>
                                                                    <rect height="1" width="1" x="2" y="3"></rect>
                                                                    <rect height="1" width="1" x="6" y="2"></rect>
                                                                    <rect height="1" width="1" x="3" y="2"></rect>
                                                                    <rect height="1" width="1" x="5" y="3"></rect>
                                                                    <rect height="1" width="1" x="9" y="2"></rect>
                                                                    <rect height="1" width="1" x="15" y="14"></rect>
                                                                    <polygon points="13.447 10.174 13.469 10.225 13.472 10.232 13.808 11 14 11 14 10 13.37 10 13.447 10.174"></polygon>
                                                                    <rect height="1" width="1" x="13" y="7"></rect>
                                                                    <rect height="1" width="1" x="15" y="5"></rect>
                                                                    <rect height="1" width="1" x="14" y="6"></rect>
                                                                    <rect height="1" width="1" x="15" y="8"></rect>
                                                                    <rect height="1" width="1" x="14" y="9"></rect>
                                                                    <path d="M3.775,14H3v1H4V14.314A1.97,1.97,0,0,1,3.775,14Z"></path>
                                                                    <rect height="1" width="1" x="14" y="3"></rect>
                                                                    <polygon points="12 6.868 12 6 11.62 6 12 6.868"></polygon>
                                                                    <rect height="1" width="1" x="15" y="2"></rect>
                                                                    <rect height="1" width="1" x="12" y="5"></rect>
                                                                    <rect height="1" width="1" x="13" y="4"></rect>
                                                                    <polygon points="12.933 9 13 9 13 8 12.495 8 12.933 9"></polygon>
                                                                    <rect height="1" width="1" x="9" y="14"></rect>
                                                                    <rect height="1" width="1" x="8" y="15"></rect>
                                                                    <path d="M6,14.926V15H7V14.316A1.993,1.993,0,0,1,6,14.926Z"></path>
                                                                    <rect height="1" width="1" x="5" y="15"></rect>
                                                                    <path d="M10.668,13.8L10.317,13H10v1h0.792A1.947,1.947,0,0,1,10.668,13.8Z"></path>
                                                                    <rect height="1" width="1" x="11" y="15"></rect>
                                                                    <path d="M14.332,12.2a1.99,1.99,0,0,1,.166.8H15V12H14.245Z"></path>
                                                                    <rect height="1" width="1" x="14" y="15"></rect>
                                                                    <rect height="1" width="1" x="15" y="11"></rect>
                                                                  </g>
                                                                  <polyline class="ql-stroke" points="5.5 13 9 5 12.5 13"></polyline>
                                                                  <line class="ql-stroke" x1="11.63" x2="6.38" y1="11" y2="11"></line>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-4"><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#000000" style="background-color: rgb(0, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#e60000" style="background-color: rgb(230, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ff9900" style="background-color: rgb(255, 153, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ffff00" style="background-color: rgb(255, 255, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#008a00" style="background-color: rgb(0, 138, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#0066cc" style="background-color: rgb(0, 102, 204);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#9933ff" style="background-color: rgb(153, 51, 255);"></span><span tabindex="0" role="button" class="ql-picker-item ql-selected"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#facccc" style="background-color: rgb(250, 204, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffebcc" style="background-color: rgb(255, 235, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffcc" style="background-color: rgb(255, 255, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce8cc" style="background-color: rgb(204, 232, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce0f5" style="background-color: rgb(204, 224, 245);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ebd6ff" style="background-color: rgb(235, 214, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#bbbbbb" style="background-color: rgb(187, 187, 187);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#f06666" style="background-color: rgb(240, 102, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffc266" style="background-color: rgb(255, 194, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffff66" style="background-color: rgb(255, 255, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66b966" style="background-color: rgb(102, 185, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66a3e0" style="background-color: rgb(102, 163, 224);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#c285ff" style="background-color: rgb(194, 133, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#888888" style="background-color: rgb(136, 136, 136);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#a10000" style="background-color: rgb(161, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b26b00" style="background-color: rgb(178, 107, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b2b200" style="background-color: rgb(178, 178, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#006100" style="background-color: rgb(0, 97, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#0047b2" style="background-color: rgb(0, 71, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#6b24b2" style="background-color: rgb(107, 36, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#444444" style="background-color: rgb(68, 68, 68);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#5c0000" style="background-color: rgb(92, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#663d00" style="background-color: rgb(102, 61, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#666600" style="background-color: rgb(102, 102, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#003700" style="background-color: rgb(0, 55, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#002966" style="background-color: rgb(0, 41, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#3d1466" style="background-color: rgb(61, 20, 102);"></span></span>
                                                            </span>
                                                            <select class="ql-background" style="display: none;">
                                                              <option value="#000000"></option>
                                                              <option value="#e60000"></option>
                                                              <option value="#ff9900"></option>
                                                              <option value="#ffff00"></option>
                                                              <option value="#008a00"></option>
                                                              <option value="#0066cc"></option>
                                                              <option value="#9933ff"></option>
                                                              <option selected="selected"></option>
                                                              <option value="#facccc"></option>
                                                              <option value="#ffebcc"></option>
                                                              <option value="#ffffcc"></option>
                                                              <option value="#cce8cc"></option>
                                                              <option value="#cce0f5"></option>
                                                              <option value="#ebd6ff"></option>
                                                              <option value="#bbbbbb"></option>
                                                              <option value="#f06666"></option>
                                                              <option value="#ffc266"></option>
                                                              <option value="#ffff66"></option>
                                                              <option value="#66b966"></option>
                                                              <option value="#66a3e0"></option>
                                                              <option value="#c285ff"></option>
                                                              <option value="#888888"></option>
                                                              <option value="#a10000"></option>
                                                              <option value="#b26b00"></option>
                                                              <option value="#b2b200"></option>
                                                              <option value="#006100"></option>
                                                              <option value="#0047b2"></option>
                                                              <option value="#6b24b2"></option>
                                                              <option value="#444444"></option>
                                                              <option value="#5c0000"></option>
                                                              <option value="#663d00"></option>
                                                              <option value="#666600"></option>
                                                              <option value="#003700"></option>
                                                              <option value="#002966"></option>
                                                              <option value="#3d1466"></option>
                                                            </select>
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
                                                            <button type="button" class="ql-list" value="check">
                                                              <svg class="" viewBox="0 0 18 18">
                                                                <line class="ql-stroke" x1="9" x2="15" y1="4" y2="4"></line>
                                                                <polyline class="ql-stroke" points="3 4 4 5 6 3"></polyline>
                                                                <line class="ql-stroke" x1="9" x2="15" y1="14" y2="14"></line>
                                                                <polyline class="ql-stroke" points="3 14 4 15 6 13"></polyline>
                                                                <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                                <polyline class="ql-stroke" points="3 9 4 10 6 8"></polyline>
                                                              </svg>
                                                            </button>
                                                          </span>
                                                          <span class="ql-formats">
                                                            <span class="ql-align ql-picker ql-icon-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-5">
                                                                <svg viewBox="0 0 18 18">
                                                                  <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                                  <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                                  <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-5">
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
                                                            <button type="button" class="ql-indent" value="-1">
                                                              <svg viewBox="0 0 18 18">
                                                                <line class="ql-stroke" x1="3" x2="15" y1="14" y2="14"></line>
                                                                <line class="ql-stroke" x1="3" x2="15" y1="4" y2="4"></line>
                                                                <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                                <polyline class="ql-stroke" points="5 7 5 11 3 9 5 7"></polyline>
                                                              </svg>
                                                            </button>
                                                            <button type="button" class="ql-indent" value="+1">
                                                              <svg viewBox="0 0 18 18">
                                                                <line class="ql-stroke" x1="3" x2="15" y1="14" y2="14"></line>
                                                                <line class="ql-stroke" x1="3" x2="15" y1="4" y2="4"></line>
                                                                <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                                <polyline class="ql-fill ql-stroke" points="3 7 3 11 5 9 3 7"></polyline>
                                                              </svg>
                                                            </button>
                                                          </span>
                                                          <span class="ql-formats">
                                                            <span class="ql-table ql-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-6" data-value="insert-table">
                                                                <svg viewBox="0 0 18 18">
                                                                  <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                                  <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-6"><span tabindex="0" role="button" class="ql-picker-item ql-selected" data-value="insert-table"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-row-above"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-row-below"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-column-right"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-column-left"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-row"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-column"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-table"></span></span>
                                                            </span>
                                                            <select class="ql-table" style="display: none;">
                                                              <option value="insert-table"></option>
                                                              <option value="insert-row-above"></option>
                                                              <option value="insert-row-below"></option>
                                                              <option value="insert-column-right"></option>
                                                              <option value="insert-column-left"></option>
                                                              <option value="delete-row"></option>
                                                              <option value="delete-column"></option>
                                                              <option value="delete-table"></option>
                                                            </select>
                                                          </span>
                                                        </div>
                                                        <div class="ql-container ql-snow" style="position: relative;">
                                                          <div class="ql-editor ql-blank" data-gramm="false" contenteditable="true">
                                                            <p><br></p>
                                                          </div>
                                                          <div class="ql-tooltip ql-hidden"><a class="ql-preview" rel="noopener noreferrer" target="_blank" href="about:blank"></a><input type="text" data-formula="e=mc^2" data-link="https://quilljs.com" data-video="Embed URL"><a class="ql-action"></a><a class="ql-remove"></a></div>
                                                        </div>
                                                      </div>
                                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                                        <div class="ql-editor read-mode"></div>
                                                      </div>
                                                      <p class="help-box small text-muted">Introduce your company to the website visitor.</p>
                                                    </div>
                                                  </div>
                                                  <span class="tooltip-content">company_introduction</span>
                                                </div>
                                              </form>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row form-section card-section visible-section" data-fieldname="sb0">
                                          <div class="section-head">
                                            Org History
                                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                          </div>
                                          <div class="section-body">
                                            <div class="form-column col-sm-12" data-fieldname="__column_2">
                                              <form>
                                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="company_history_heading">
                                                  <div class="form-group">
                                                    <div class="clearfix">
                                                      <label class="control-label" style="padding-right: 0px;">Org History Heading</label>
                                                      <span class="help"></span>
                                                    </div>
                                                    <div class="control-input-wrapper">
                                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                                      <p class="help-box small text-muted">"Company History"</p>
                                                    </div>
                                                  </div>
                                                  <span class="tooltip-content">company_history_heading</span>
                                                </div>
                                                <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="company_history">
                                                  <span class="tooltip-content">company_history</span>
                                                  <div class="grid-field">
                                                    <label class="control-label">Org History</label>
                                                    <span class="help"></span>
                                                    <p class="text-muted small grid-description" style="display: none;"></p>
                                                    <div class="grid-custom-buttons"></div>
                                                    <div class="form-grid-container">
                                                      <div class="form-grid">
                                                        <div class="grid-heading-row">
                                                          <div class="grid-row">
                                                            <div class="data-row row">
                                                              <div class="row-check sortable-handle col">
                                                                <input type="checkbox" class="grid-row-check">
                                                              </div>
                                                              <div class="row-index sortable-handle col">
                                                                <span>No.</span>
                                                              </div>
                                                              <div class="col grid-static-col col-xs-5 " data-fieldname="year" data-fieldtype="Data" title="Year">
                                                                <div class="field-area" style="display: none;"></div>
                                                                <div class="static-area ellipsis">Year</div>
                                                              </div>
                                                              <div class="col grid-static-col col-xs-5  grid-overflow-no-ellipsis" data-fieldname="highlight" data-fieldtype="Text" title="Highlight">
                                                                <div class="field-area" style="display: none;"></div>
                                                                <div class="static-area ellipsis">Highlight</div>
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
                                        <div class="row form-section card-section visible-section" data-fieldname="sb1">
                                          <div class="section-head">
                                            Team Members
                                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                          </div>
                                          <div class="section-body">
                                            <div class="form-column col-sm-12" data-fieldname="__column_3">
                                              <form>
                                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="team_members_heading">
                                                  <div class="form-group">
                                                    <div class="clearfix">
                                                      <label class="control-label" style="padding-right: 0px;">Team Members Heading</label>
                                                      <span class="help"></span>
                                                    </div>
                                                    <div class="control-input-wrapper">
                                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                                      <p class="help-box small text-muted">"Team Members" or "Management"</p>
                                                    </div>
                                                  </div>
                                                  <span class="tooltip-content">team_members_heading</span>
                                                </div>
                                                <div class="frappe-control" data-fieldtype="Small Text" data-fieldname="team_members_subtitle">
                                                  <div class="form-group">
                                                    <div class="clearfix">
                                                      <label class="control-label" style="padding-right: 0px;">Team Members Subtitle</label>
                                                      <span class="help"></span>
                                                    </div>
                                                    <div class="control-input-wrapper">
                                                      <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Small Text" data-fieldname="team_members_subtitle" placeholder="" data-doctype="About Us Settings" style="height: 150px;"></textarea></div>
                                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                                      <p class="help-box small text-muted"></p>
                                                    </div>
                                                  </div>
                                                  <span class="tooltip-content">team_members_subtitle</span>
                                                </div>
                                                <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="team_members">
                                                  <span class="tooltip-content">team_members</span>
                                                  <div class="grid-field">
                                                    <label class="control-label">Team Members</label>
                                                    <span class="help"></span>
                                                    <p class="text-muted small grid-description" style="display: none;"></p>
                                                    <div class="grid-custom-buttons"></div>
                                                    <div class="form-grid-container">
                                                      <div class="form-grid">
                                                        <div class="grid-heading-row">
                                                          <div class="grid-row">
                                                            <div class="data-row row">
                                                              <div class="row-check sortable-handle col">
                                                                <input type="checkbox" class="grid-row-check">
                                                              </div>
                                                              <div class="row-index sortable-handle col">
                                                                <span>No.</span>
                                                              </div>
                                                              <div class="col grid-static-col col-xs-3 " data-fieldname="full_name" data-fieldtype="Data" title="Full Name">
                                                                <div class="field-area" style="display: none;"></div>
                                                                <div class="static-area ellipsis reqd">Full Name</div>
                                                              </div>
                                                              <div class="col grid-static-col col-xs-3 " data-fieldname="image_link" data-fieldtype="Attach" title="Image Link">
                                                                <div class="field-area" style="display: none;"></div>
                                                                <div class="static-area ellipsis">Image Link</div>
                                                              </div>
                                                              <div class="col grid-static-col col-xs-4  grid-overflow-no-ellipsis" data-fieldname="bio" data-fieldtype="Small Text" title="Bio">
                                                                <div class="field-area" style="display: none;"></div>
                                                                <div class="static-area ellipsis reqd">Bio</div>
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
                                                <div class="frappe-control" data-fieldtype="Text Editor" data-fieldname="footer">
                                                  <div class="form-group">
                                                    <div class="clearfix">
                                                      <label class="control-label" style="padding-right: 0px;">Footer</label>
                                                      <span class="help"></span>
                                                    </div>
                                                    <div class="control-input-wrapper">
                                                      <div class="control-input">
                                                        <div class="ql-toolbar ql-snow">
                                                          <span class="ql-formats">
                                                            <span class="ql-header ql-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-7">
                                                                <svg viewBox="0 0 18 18">
                                                                  <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                                  <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-7"><span tabindex="0" role="button" class="ql-picker-item" data-value="1"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="2"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="3"></span><span tabindex="0" role="button" class="ql-picker-item ql-selected"></span></span>
                                                            </span>
                                                            <select class="ql-header" style="display: none;">
                                                              <option value="1"></option>
                                                              <option value="2"></option>
                                                              <option value="3"></option>
                                                              <option selected="selected"></option>
                                                            </select>
                                                          </span>
                                                          <span class="ql-formats">
                                                            <span class="ql-size ql-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-8" data-value="---">
                                                                <svg viewBox="0 0 18 18">
                                                                  <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                                  <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-8"><span tabindex="0" role="button" class="ql-picker-item ql-selected" data-value="---"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="8px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="9px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="10px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="11px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="12px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="13px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="14px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="15px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="16px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="18px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="20px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="22px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="24px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="32px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="36px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="40px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="48px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="54px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="64px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="96px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="128px"></span></span>
                                                            </span>
                                                            <select class="ql-size" style="display: none;">
                                                              <option selected="selected"></option>
                                                              <option value="8px"></option>
                                                              <option value="9px"></option>
                                                              <option value="10px"></option>
                                                              <option value="11px"></option>
                                                              <option value="12px"></option>
                                                              <option value="13px"></option>
                                                              <option value="14px"></option>
                                                              <option value="15px"></option>
                                                              <option value="16px"></option>
                                                              <option value="18px"></option>
                                                              <option value="20px"></option>
                                                              <option value="22px"></option>
                                                              <option value="24px"></option>
                                                              <option value="32px"></option>
                                                              <option value="36px"></option>
                                                              <option value="40px"></option>
                                                              <option value="48px"></option>
                                                              <option value="54px"></option>
                                                              <option value="64px"></option>
                                                              <option value="96px"></option>
                                                              <option value="128px"></option>
                                                            </select>
                                                          </span>
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
                                                          <span class="ql-formats">
                                                            <span class="ql-color ql-picker ql-color-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-9">
                                                                <svg viewBox="0 0 18 18">
                                                                  <line class="ql-color-label ql-stroke ql-transparent" x1="3" x2="15" y1="15" y2="15"></line>
                                                                  <polyline class="ql-stroke" points="5.5 11 9 3 12.5 11"></polyline>
                                                                  <line class="ql-stroke" x1="11.63" x2="6.38" y1="9" y2="9"></line>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-9"><span tabindex="0" role="button" class="ql-picker-item ql-selected ql-primary"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#e60000" style="background-color: rgb(230, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ff9900" style="background-color: rgb(255, 153, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ffff00" style="background-color: rgb(255, 255, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#008a00" style="background-color: rgb(0, 138, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#0066cc" style="background-color: rgb(0, 102, 204);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#9933ff" style="background-color: rgb(153, 51, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffff" style="background-color: rgb(255, 255, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#facccc" style="background-color: rgb(250, 204, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffebcc" style="background-color: rgb(255, 235, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffcc" style="background-color: rgb(255, 255, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce8cc" style="background-color: rgb(204, 232, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce0f5" style="background-color: rgb(204, 224, 245);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ebd6ff" style="background-color: rgb(235, 214, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#bbbbbb" style="background-color: rgb(187, 187, 187);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#f06666" style="background-color: rgb(240, 102, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffc266" style="background-color: rgb(255, 194, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffff66" style="background-color: rgb(255, 255, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66b966" style="background-color: rgb(102, 185, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66a3e0" style="background-color: rgb(102, 163, 224);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#c285ff" style="background-color: rgb(194, 133, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#888888" style="background-color: rgb(136, 136, 136);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#a10000" style="background-color: rgb(161, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b26b00" style="background-color: rgb(178, 107, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b2b200" style="background-color: rgb(178, 178, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#006100" style="background-color: rgb(0, 97, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#0047b2" style="background-color: rgb(0, 71, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#6b24b2" style="background-color: rgb(107, 36, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#444444" style="background-color: rgb(68, 68, 68);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#5c0000" style="background-color: rgb(92, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#663d00" style="background-color: rgb(102, 61, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#666600" style="background-color: rgb(102, 102, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#003700" style="background-color: rgb(0, 55, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#002966" style="background-color: rgb(0, 41, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#3d1466" style="background-color: rgb(61, 20, 102);"></span></span>
                                                            </span>
                                                            <select class="ql-color" style="display: none;">
                                                              <option selected="selected"></option>
                                                              <option value="#e60000"></option>
                                                              <option value="#ff9900"></option>
                                                              <option value="#ffff00"></option>
                                                              <option value="#008a00"></option>
                                                              <option value="#0066cc"></option>
                                                              <option value="#9933ff"></option>
                                                              <option value="#ffffff"></option>
                                                              <option value="#facccc"></option>
                                                              <option value="#ffebcc"></option>
                                                              <option value="#ffffcc"></option>
                                                              <option value="#cce8cc"></option>
                                                              <option value="#cce0f5"></option>
                                                              <option value="#ebd6ff"></option>
                                                              <option value="#bbbbbb"></option>
                                                              <option value="#f06666"></option>
                                                              <option value="#ffc266"></option>
                                                              <option value="#ffff66"></option>
                                                              <option value="#66b966"></option>
                                                              <option value="#66a3e0"></option>
                                                              <option value="#c285ff"></option>
                                                              <option value="#888888"></option>
                                                              <option value="#a10000"></option>
                                                              <option value="#b26b00"></option>
                                                              <option value="#b2b200"></option>
                                                              <option value="#006100"></option>
                                                              <option value="#0047b2"></option>
                                                              <option value="#6b24b2"></option>
                                                              <option value="#444444"></option>
                                                              <option value="#5c0000"></option>
                                                              <option value="#663d00"></option>
                                                              <option value="#666600"></option>
                                                              <option value="#003700"></option>
                                                              <option value="#002966"></option>
                                                              <option value="#3d1466"></option>
                                                            </select>
                                                            <span class="ql-background ql-picker ql-color-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-10">
                                                                <svg viewBox="0 0 18 18">
                                                                  <g class="ql-fill ql-color-label">
                                                                    <polygon points="6 6.868 6 6 5 6 5 7 5.942 7 6 6.868"></polygon>
                                                                    <rect height="1" width="1" x="4" y="4"></rect>
                                                                    <polygon points="6.817 5 6 5 6 6 6.38 6 6.817 5"></polygon>
                                                                    <rect height="1" width="1" x="2" y="6"></rect>
                                                                    <rect height="1" width="1" x="3" y="5"></rect>
                                                                    <rect height="1" width="1" x="4" y="7"></rect>
                                                                    <polygon points="4 11.439 4 11 3 11 3 12 3.755 12 4 11.439"></polygon>
                                                                    <rect height="1" width="1" x="2" y="12"></rect>
                                                                    <rect height="1" width="1" x="2" y="9"></rect>
                                                                    <rect height="1" width="1" x="2" y="15"></rect>
                                                                    <polygon points="4.63 10 4 10 4 11 4.192 11 4.63 10"></polygon>
                                                                    <rect height="1" width="1" x="3" y="8"></rect>
                                                                    <path d="M10.832,4.2L11,4.582V4H10.708A1.948,1.948,0,0,1,10.832,4.2Z"></path>
                                                                    <path d="M7,4.582L7.168,4.2A1.929,1.929,0,0,1,7.292,4H7V4.582Z"></path>
                                                                    <path d="M8,13H7.683l-0.351.8a1.933,1.933,0,0,1-.124.2H8V13Z"></path>
                                                                    <rect height="1" width="1" x="12" y="2"></rect>
                                                                    <rect height="1" width="1" x="11" y="3"></rect>
                                                                    <path d="M9,3H8V3.282A1.985,1.985,0,0,1,9,3Z"></path>
                                                                    <rect height="1" width="1" x="2" y="3"></rect>
                                                                    <rect height="1" width="1" x="6" y="2"></rect>
                                                                    <rect height="1" width="1" x="3" y="2"></rect>
                                                                    <rect height="1" width="1" x="5" y="3"></rect>
                                                                    <rect height="1" width="1" x="9" y="2"></rect>
                                                                    <rect height="1" width="1" x="15" y="14"></rect>
                                                                    <polygon points="13.447 10.174 13.469 10.225 13.472 10.232 13.808 11 14 11 14 10 13.37 10 13.447 10.174"></polygon>
                                                                    <rect height="1" width="1" x="13" y="7"></rect>
                                                                    <rect height="1" width="1" x="15" y="5"></rect>
                                                                    <rect height="1" width="1" x="14" y="6"></rect>
                                                                    <rect height="1" width="1" x="15" y="8"></rect>
                                                                    <rect height="1" width="1" x="14" y="9"></rect>
                                                                    <path d="M3.775,14H3v1H4V14.314A1.97,1.97,0,0,1,3.775,14Z"></path>
                                                                    <rect height="1" width="1" x="14" y="3"></rect>
                                                                    <polygon points="12 6.868 12 6 11.62 6 12 6.868"></polygon>
                                                                    <rect height="1" width="1" x="15" y="2"></rect>
                                                                    <rect height="1" width="1" x="12" y="5"></rect>
                                                                    <rect height="1" width="1" x="13" y="4"></rect>
                                                                    <polygon points="12.933 9 13 9 13 8 12.495 8 12.933 9"></polygon>
                                                                    <rect height="1" width="1" x="9" y="14"></rect>
                                                                    <rect height="1" width="1" x="8" y="15"></rect>
                                                                    <path d="M6,14.926V15H7V14.316A1.993,1.993,0,0,1,6,14.926Z"></path>
                                                                    <rect height="1" width="1" x="5" y="15"></rect>
                                                                    <path d="M10.668,13.8L10.317,13H10v1h0.792A1.947,1.947,0,0,1,10.668,13.8Z"></path>
                                                                    <rect height="1" width="1" x="11" y="15"></rect>
                                                                    <path d="M14.332,12.2a1.99,1.99,0,0,1,.166.8H15V12H14.245Z"></path>
                                                                    <rect height="1" width="1" x="14" y="15"></rect>
                                                                    <rect height="1" width="1" x="15" y="11"></rect>
                                                                  </g>
                                                                  <polyline class="ql-stroke" points="5.5 13 9 5 12.5 13"></polyline>
                                                                  <line class="ql-stroke" x1="11.63" x2="6.38" y1="11" y2="11"></line>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-10"><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#000000" style="background-color: rgb(0, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#e60000" style="background-color: rgb(230, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ff9900" style="background-color: rgb(255, 153, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ffff00" style="background-color: rgb(255, 255, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#008a00" style="background-color: rgb(0, 138, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#0066cc" style="background-color: rgb(0, 102, 204);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#9933ff" style="background-color: rgb(153, 51, 255);"></span><span tabindex="0" role="button" class="ql-picker-item ql-selected"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#facccc" style="background-color: rgb(250, 204, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffebcc" style="background-color: rgb(255, 235, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffcc" style="background-color: rgb(255, 255, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce8cc" style="background-color: rgb(204, 232, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce0f5" style="background-color: rgb(204, 224, 245);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ebd6ff" style="background-color: rgb(235, 214, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#bbbbbb" style="background-color: rgb(187, 187, 187);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#f06666" style="background-color: rgb(240, 102, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffc266" style="background-color: rgb(255, 194, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffff66" style="background-color: rgb(255, 255, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66b966" style="background-color: rgb(102, 185, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66a3e0" style="background-color: rgb(102, 163, 224);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#c285ff" style="background-color: rgb(194, 133, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#888888" style="background-color: rgb(136, 136, 136);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#a10000" style="background-color: rgb(161, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b26b00" style="background-color: rgb(178, 107, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b2b200" style="background-color: rgb(178, 178, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#006100" style="background-color: rgb(0, 97, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#0047b2" style="background-color: rgb(0, 71, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#6b24b2" style="background-color: rgb(107, 36, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#444444" style="background-color: rgb(68, 68, 68);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#5c0000" style="background-color: rgb(92, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#663d00" style="background-color: rgb(102, 61, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#666600" style="background-color: rgb(102, 102, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#003700" style="background-color: rgb(0, 55, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#002966" style="background-color: rgb(0, 41, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#3d1466" style="background-color: rgb(61, 20, 102);"></span></span>
                                                            </span>
                                                            <select class="ql-background" style="display: none;">
                                                              <option value="#000000"></option>
                                                              <option value="#e60000"></option>
                                                              <option value="#ff9900"></option>
                                                              <option value="#ffff00"></option>
                                                              <option value="#008a00"></option>
                                                              <option value="#0066cc"></option>
                                                              <option value="#9933ff"></option>
                                                              <option selected="selected"></option>
                                                              <option value="#facccc"></option>
                                                              <option value="#ffebcc"></option>
                                                              <option value="#ffffcc"></option>
                                                              <option value="#cce8cc"></option>
                                                              <option value="#cce0f5"></option>
                                                              <option value="#ebd6ff"></option>
                                                              <option value="#bbbbbb"></option>
                                                              <option value="#f06666"></option>
                                                              <option value="#ffc266"></option>
                                                              <option value="#ffff66"></option>
                                                              <option value="#66b966"></option>
                                                              <option value="#66a3e0"></option>
                                                              <option value="#c285ff"></option>
                                                              <option value="#888888"></option>
                                                              <option value="#a10000"></option>
                                                              <option value="#b26b00"></option>
                                                              <option value="#b2b200"></option>
                                                              <option value="#006100"></option>
                                                              <option value="#0047b2"></option>
                                                              <option value="#6b24b2"></option>
                                                              <option value="#444444"></option>
                                                              <option value="#5c0000"></option>
                                                              <option value="#663d00"></option>
                                                              <option value="#666600"></option>
                                                              <option value="#003700"></option>
                                                              <option value="#002966"></option>
                                                              <option value="#3d1466"></option>
                                                            </select>
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
                                                            <button type="button" class="ql-list" value="check">
                                                              <svg class="" viewBox="0 0 18 18">
                                                                <line class="ql-stroke" x1="9" x2="15" y1="4" y2="4"></line>
                                                                <polyline class="ql-stroke" points="3 4 4 5 6 3"></polyline>
                                                                <line class="ql-stroke" x1="9" x2="15" y1="14" y2="14"></line>
                                                                <polyline class="ql-stroke" points="3 14 4 15 6 13"></polyline>
                                                                <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                                <polyline class="ql-stroke" points="3 9 4 10 6 8"></polyline>
                                                              </svg>
                                                            </button>
                                                          </span>
                                                          <span class="ql-formats">
                                                            <span class="ql-align ql-picker ql-icon-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-11">
                                                                <svg viewBox="0 0 18 18">
                                                                  <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                                  <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                                  <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-11">
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
                                                            <button type="button" class="ql-indent" value="-1">
                                                              <svg viewBox="0 0 18 18">
                                                                <line class="ql-stroke" x1="3" x2="15" y1="14" y2="14"></line>
                                                                <line class="ql-stroke" x1="3" x2="15" y1="4" y2="4"></line>
                                                                <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                                <polyline class="ql-stroke" points="5 7 5 11 3 9 5 7"></polyline>
                                                              </svg>
                                                            </button>
                                                            <button type="button" class="ql-indent" value="+1">
                                                              <svg viewBox="0 0 18 18">
                                                                <line class="ql-stroke" x1="3" x2="15" y1="14" y2="14"></line>
                                                                <line class="ql-stroke" x1="3" x2="15" y1="4" y2="4"></line>
                                                                <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                                <polyline class="ql-fill ql-stroke" points="3 7 3 11 5 9 3 7"></polyline>
                                                              </svg>
                                                            </button>
                                                          </span>
                                                          <span class="ql-formats">
                                                            <span class="ql-table ql-picker">
                                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-12" data-value="insert-table">
                                                                <svg viewBox="0 0 18 18">
                                                                  <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                                  <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                                </svg>
                                                              </span>
                                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-12"><span tabindex="0" role="button" class="ql-picker-item ql-selected" data-value="insert-table"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-row-above"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-row-below"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-column-right"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-column-left"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-row"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-column"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-table"></span></span>
                                                            </span>
                                                            <select class="ql-table" style="display: none;">
                                                              <option value="insert-table"></option>
                                                              <option value="insert-row-above"></option>
                                                              <option value="insert-row-below"></option>
                                                              <option value="insert-column-right"></option>
                                                              <option value="insert-column-left"></option>
                                                              <option value="delete-row"></option>
                                                              <option value="delete-column"></option>
                                                              <option value="delete-table"></option>
                                                            </select>
                                                          </span>
                                                        </div>
                                                        <div class="ql-container ql-snow" style="position: relative;">
                                                          <div class="ql-editor ql-blank" data-gramm="false" contenteditable="true">
                                                            <p><br></p>
                                                          </div>
                                                          <div class="ql-tooltip ql-hidden"><a class="ql-preview" rel="noopener noreferrer" target="_blank" href="about:blank"></a><input type="text" data-formula="e=mc^2" data-link="https://quilljs.com" data-video="Embed URL"><a class="ql-action"></a><a class="ql-remove"></a></div>
                                                        </div>
                                                      </div>
                                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                                        <div class="ql-editor read-mode"></div>
                                                      </div>
                                                      <p class="help-box small text-muted">More content for the bottom of the page.</p>
                                                    </div>
                                                  </div>
                                                  <span class="tooltip-content">footer</span>
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
                              <div>
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
                                        <div class="timeline-items timeline-actions" style="">
                                          <div class="timeline-item">
                                            <div class="timeline-content action-buttons">
                                              <button class="btn btn-xs btn-secondary action-btn">
                                                <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                                  <use class="" href="#es-line-add"></use>
                                                </svg>
                                                New Email
                                              </button>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="timeline-items">
                                        <div class="timeline-item">
                                          <div class="timeline-dot"></div>
                                          <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a> created this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
                                        </div>
                                        <div class="timeline-item" data-timestamp="2024-11-23 15:41:12.024844">
                                          <div class="timeline-dot"></div>
                                          <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a> last edited this<span> · <span class="frappe-timestamp " data-timestamp="2024-11-23 15:41:12.024844" title="23-11-2024 15:41:12">yesterday</span></span></div>
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