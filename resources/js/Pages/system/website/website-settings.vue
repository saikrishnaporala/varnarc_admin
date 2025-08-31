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
                  <b-tabs v-model="activeTab" pills>
                    <b-tab title="Home">
                      <div class="tab-pane fade show active" id="website-settings-home_tab" role="tabpanel" aria-labelledby="website-settings-home_tab-tab">
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
                              <div id="heatmap-website_settings" class="heatmap"></div>
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
                        <div class="row form-section card-section visible-section" data-fieldname="sb0">
                          <div class="section-head">
                            Landing Page
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_3">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="home_page">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Home Page</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted">Link that is the website home page. Standard Links (home, login, products, blog, about, contact)</p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">home_page</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="cb4">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="title_prefix">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Title Prefix</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted">Show title in browser window as "Prefix - title"</p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">title_prefix</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="misc_section">
                          <div class="section-head">
                            Login Page
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_4">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="app_name">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">App Name</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;">Frappe</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">app_name</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disable_signup">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disable_signup" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                    <span class="label-area">Disable signups</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted">New users will have to be manually registered by system managers.</p>
                                  </div>
                                  <span class="tooltip-content">disable_signup</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_footer_on_login">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_footer_on_login" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Show footer on login</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">show_footer_on_login</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="column_break_9">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Attach Image" data-fieldname="app_logo">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">App Logo</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="attached-file flex justify-between align-center" style="display: none;">
                                          <div class="ellipsis">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-link"></use>
                                            </svg>
                                            <a class="attached-file-link" target="_blank" data-original-title="" title=""></a>
                                          </div>
                                          <div>
                                            <a class="btn btn-xs btn-default" data-action="reload_attachment" style="display: none;">Reload File</a>
                                            <a class="btn btn-xs btn-default" data-action="clear_attachment">Clear</a>
                                          </div>
                                        </div>
                                        <button class="btn btn-default btn-sm btn-attach" data-fieldtype="Attach Image" data-fieldname="app_logo" placeholder="" data-doctype="Website Settings">Attach</button>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">app_logo</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="section_break_6">
                          <div class="section-head">
                            Theme
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_5">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="website_theme">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Website Theme</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="link-field ui-front" style="position: relative;">
                                          <div class="awesomplete">
                                            <input type="text" class="input-with-feedback form-control" data-target="Website Theme" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
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
                                      <div class="control-value like-disabled-input" style="display: none;"><a href="/app/website-theme/Standard" data-doctype="Website Theme" data-name="Standard" data-value="Standard">Standard</a></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">website_theme</span>
                                </div>
                                <div class="frappe-control hide-control" data-fieldtype="Image" data-fieldname="website_theme_image" style="margin: 0px;">
                                  <span class="tooltip-content">website_theme_image</span>
                                  <div style="margin-bottom: 10px;">
                                    <div class="missing-image">
                                      <svg class="icon  icon-md" style="" aria-hidden="true">
                                        <use class="" href="#icon-restriction"></use>
                                      </svg>
                                    </div>
                                  </div>
                                  <div class="clearfix"></div>
                                </div>
                                <div class="frappe-control hide-control" data-fieldtype="Code" data-fieldname="website_theme_image_link">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;"></label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"></div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">website_theme_image_link</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Navbar">
                      <div class="tab-pane fade show" id="website-settings-navbar_tab" role="tabpanel" aria-labelledby="website-settings-navbar_tab-tab">
                        <div class="row form-section card-section empty-section" data-fieldname="__section_6">
                          <div class="section-body"></div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="brand">
                          <div class="section-head collapsible collapsed">
                            Brand
                            <span class="ml-2 collapse-indicator mb-1" style="">
                              <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                <use class="mb-1" href="#es-line-down"></use>
                              </svg>
                            </span>
                          </div>
                          <div class="section-body hide">
                            <div class="form-column col-sm-12" data-fieldname="__column_7">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Attach Image" data-fieldname="banner_image">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Brand Image</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="attached-file flex justify-between align-center" style="display: none;">
                                          <div class="ellipsis">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-link"></use>
                                            </svg>
                                            <a class="attached-file-link" target="_blank" data-original-title="" title=""></a>
                                          </div>
                                          <div>
                                            <a class="btn btn-xs btn-default" data-action="reload_attachment" style="display: none;">Reload File</a>
                                            <a class="btn btn-xs btn-default" data-action="clear_attachment">Clear</a>
                                          </div>
                                        </div>
                                        <button class="btn btn-default btn-sm btn-attach" data-fieldtype="Attach Image" data-fieldname="banner_image" placeholder="" data-doctype="Website Settings">Attach</button>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted">Select an image of approx width 150px with a transparent background for best results.</p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">banner_image</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Attach Image" data-fieldname="splash_image">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Splash Image</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="attached-file flex justify-between align-center" style="display: none;">
                                          <div class="ellipsis">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-link"></use>
                                            </svg>
                                            <a class="attached-file-link" target="_blank" data-original-title="" title=""></a>
                                          </div>
                                          <div>
                                            <a class="btn btn-xs btn-default" data-action="reload_attachment" style="display: none;">Reload File</a>
                                            <a class="btn btn-xs btn-default" data-action="clear_attachment">Clear</a>
                                          </div>
                                        </div>
                                        <button class="btn btn-default btn-sm btn-attach" data-fieldtype="Attach Image" data-fieldname="splash_image" placeholder="" data-doctype="Website Settings">Attach</button>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">splash_image</span>
                                </div>
                                <div class="frappe-control" data-fieldtype="Code" data-fieldname="brand_html">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Brand HTML</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="ace-editor-target border rounded ace_editor ace_hidpi ace-tomorrow" style="height: 300px;">
                                          <textarea class="ace_text-input" wrap="off" autocorrect="off" autocapitalize="off" spellcheck="false" aria-haspopup="false" aria-autocomplete="both" role="textbox" style="opacity: 0; font-size: 1px;"></textarea>
                                          <div class="ace_gutter" aria-hidden="true">
                                            <div class="ace_layer ace_gutter-layer ace_folding-enabled" style="height: 1e+06px;"></div>
                                          </div>
                                          <div class="ace_scroller" style="line-height: 0px;">
                                            <div class="ace_content">
                                              <div class="ace_layer ace_print-margin-layer">
                                                <div class="ace_print-margin" style="left: 4px; visibility: hidden;"></div>
                                              </div>
                                              <div class="ace_layer ace_marker-layer"></div>
                                              <div class="ace_layer ace_text-layer" style="height: 1e+06px; margin: 0px 4px;"></div>
                                              <div class="ace_layer ace_marker-layer"></div>
                                              <div class="ace_layer ace_cursor-layer ace_hidden-cursors">
                                                <div class="ace_cursor"></div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="ace_scrollbar ace_scrollbar-v" style="display: none; width: 20px;">
                                            <div class="ace_scrollbar-inner" style="width: 20px;">&nbsp;</div>
                                          </div>
                                          <div class="ace_scrollbar ace_scrollbar-h" style="display: none; height: 20px;">
                                            <div class="ace_scrollbar-inner" style="height: 20px;">&nbsp;</div>
                                          </div>
                                          <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: hidden;">
                                            <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;"></div>
                                            <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                        <pre></pre>
                                      </div>
                                      <p class="help-box small text-muted">Brand is what appears on the top-left of the toolbar. If it is an image, make sure it
                                        has a transparent background and use the &lt;img /&gt; tag. Keep size as 200px x 30px
                                      </p>
                                      <button class="btn btn-xs btn-default">Expand</button>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">brand_html</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Button" data-fieldname="set_banner_from_image">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label hide" style="padding-right: 0px;">&nbsp;</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><button class="btn btn-xs btn-default" data-fieldtype="Button" data-fieldname="set_banner_from_image" placeholder="" data-doctype="Website Settings" value="">Set Banner from Image</button></div>
                                      <div class="hide" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">set_banner_from_image</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Attach" data-fieldname="favicon">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">FavIcon</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="attached-file flex justify-between align-center" style="display: none;">
                                          <div class="ellipsis">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-link"></use>
                                            </svg>
                                            <a class="attached-file-link" target="_blank"></a>
                                          </div>
                                          <div>
                                            <a class="btn btn-xs btn-default" data-action="reload_attachment" style="display: none;">Reload File</a>
                                            <a class="btn btn-xs btn-default" data-action="clear_attachment">Clear</a>
                                          </div>
                                        </div>
                                        <button class="btn btn-default btn-sm btn-attach" data-fieldtype="Attach" data-fieldname="favicon" placeholder="" data-doctype="Website Settings">Attach</button>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted">An icon file with .ico extension. Should be 16 x 16 px. Generated using a favicon generator. [favicon-generator.org]</p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">favicon</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="top_bar">
                          <div class="section-head">
                            Navbar
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_8">
                              <form>
                                <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="top_bar_items">
                                  <span class="tooltip-content">top_bar_items</span>
                                  <div class="grid-field">
                                    <label class="control-label">Top Bar Items</label>
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
                                              <div class="col grid-static-col col-xs-4 " data-fieldname="label" data-fieldtype="Data" title="Label">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis reqd">Label</div>
                                              </div>
                                              <div class="col grid-static-col col-xs-3 " data-fieldname="url" data-fieldtype="Data" title="URL">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis">URL</div>
                                              </div>
                                              <div class="col grid-static-col col-xs-3 " data-fieldname="parent_label" data-fieldtype="Select" title="Parent Label">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis">Parent Label</div>
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
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="hide_login">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="hide_login" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Hide Login</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">hide_login</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="navbar_search">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="navbar_search" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Include Search in Top Bar</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">navbar_search</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_language_picker">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_language_picker" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Show Language Picker</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">show_language_picker</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="navbar_template_section">
                          <div class="section-head collapsible collapsed">
                            Navbar Template
                            <span class="ml-2 collapse-indicator mb-1" style="">
                              <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                <use class="mb-1" href="#es-line-down"></use>
                              </svg>
                            </span>
                          </div>
                          <div class="section-body hide">
                            <div class="form-column col-sm-12" data-fieldname="__column_9">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="navbar_template">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Navbar Template</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="link-field ui-front" style="position: relative;">
                                          <div class="awesomplete">
                                            <input type="text" class="input-with-feedback form-control" data-target="Web Template" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
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
                                  <span class="tooltip-content">navbar_template</span>
                                </div>
                                <div class="frappe-control hide-control" data-fieldtype="Code" data-fieldname="navbar_template_values">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;"></label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"></div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">navbar_template_values</span>
                                </div>
                                <div class="frappe-control input-max-width hide-control" data-fieldtype="Button" data-fieldname="edit_navbar_template_values">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label hide" style="padding-right: 0px;">&nbsp;</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><button class="btn btn-xs btn-default" data-fieldtype="Button" data-fieldname="edit_navbar_template_values" placeholder="" data-doctype="Website Settings" value="">Edit Values</button></div>
                                      <div class="hide" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">edit_navbar_template_values</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="call_to_action">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Call To Action</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">call_to_action</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="call_to_action_url">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Call To Action URL</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">call_to_action_url</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="banner">
                          <div class="section-head collapsible collapsed">
                            Banner
                            <span class="ml-2 collapse-indicator mb-1" style="">
                              <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                <use class="mb-1" href="#es-line-down"></use>
                              </svg>
                            </span>
                          </div>
                          <div class="section-body hide">
                            <div class="form-column col-sm-12" data-fieldname="__column_10">
                              <form>
                                <div class="frappe-control" data-fieldtype="Code" data-fieldname="banner_html">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Banner HTML</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="ace-editor-target border rounded ace_editor ace_hidpi ace-tomorrow" style="height: 300px;">
                                          <textarea class="ace_text-input" wrap="off" autocorrect="off" autocapitalize="off" spellcheck="false" aria-haspopup="false" aria-autocomplete="both" role="textbox" style="opacity: 0; font-size: 1px;"></textarea>
                                          <div class="ace_gutter" aria-hidden="true">
                                            <div class="ace_layer ace_gutter-layer ace_folding-enabled" style="height: 1e+06px;"></div>
                                          </div>
                                          <div class="ace_scroller" style="line-height: 0px;">
                                            <div class="ace_content">
                                              <div class="ace_layer ace_print-margin-layer">
                                                <div class="ace_print-margin" style="left: 4px; visibility: hidden;"></div>
                                              </div>
                                              <div class="ace_layer ace_marker-layer"></div>
                                              <div class="ace_layer ace_text-layer" style="height: 1e+06px; margin: 0px 4px;"></div>
                                              <div class="ace_layer ace_marker-layer"></div>
                                              <div class="ace_layer ace_cursor-layer ace_hidden-cursors">
                                                <div class="ace_cursor"></div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="ace_scrollbar ace_scrollbar-v" style="display: none; width: 20px;">
                                            <div class="ace_scrollbar-inner" style="width: 20px;">&nbsp;</div>
                                          </div>
                                          <div class="ace_scrollbar ace_scrollbar-h" style="display: none; height: 20px;">
                                            <div class="ace_scrollbar-inner" style="height: 20px;">&nbsp;</div>
                                          </div>
                                          <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: hidden;">
                                            <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;"></div>
                                            <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                        <pre></pre>
                                      </div>
                                      <p class="help-box small text-muted">Banner is above the Top Menu Bar.</p>
                                      <button class="btn btn-xs btn-default">Expand</button>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">banner_html</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Footer">
                      <div class="tab-pane fade show" id="website-settings-footer_tab" role="tabpanel" aria-labelledby="website-settings-footer_tab-tab">
                        <div class="row form-section card-section empty-section" data-fieldname="__section_11">
                          <div class="section-body"></div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="footer">
                          <div class="section-head">
                            Footer Items
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_12">
                              <form>
                                <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="footer_items">
                                  <span class="tooltip-content">footer_items</span>
                                  <div class="grid-field">
                                    <label class="control-label">Footer Items</label>
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
                                              <div class="col grid-static-col col-xs-4 " data-fieldname="label" data-fieldtype="Data" title="Label">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis reqd">Label</div>
                                              </div>
                                              <div class="col grid-static-col col-xs-3 " data-fieldname="url" data-fieldtype="Data" title="URL">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis">URL</div>
                                              </div>
                                              <div class="col grid-static-col col-xs-3 " data-fieldname="parent_label" data-fieldtype="Select" title="Parent Label">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis">Parent Label</div>
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
                        <div class="row form-section card-section visible-section" data-fieldname="footer_details_section">
                          <div class="section-head">
                            Footer Details
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_13">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="copyright">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Copyright</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">copyright</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Attach Image" data-fieldname="footer_logo">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Footer Logo</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="attached-file flex justify-between align-center" style="display: none;">
                                          <div class="ellipsis">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-link"></use>
                                            </svg>
                                            <a class="attached-file-link" target="_blank" data-original-title="" title=""></a>
                                          </div>
                                          <div>
                                            <a class="btn btn-xs btn-default" data-action="reload_attachment" style="display: none;">Reload File</a>
                                            <a class="btn btn-xs btn-default" data-action="clear_attachment">Clear</a>
                                          </div>
                                        </div>
                                        <button class="btn btn-default btn-sm btn-attach" data-fieldtype="Attach Image" data-fieldname="footer_logo" placeholder="" data-doctype="Website Settings">Attach</button>
                                      </div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">footer_logo</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="hide_footer_signup">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="hide_footer_signup" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Hide footer signup</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">hide_footer_signup</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="column_break_37">
                              <form>
                                <div class="frappe-control" data-fieldtype="Small Text" data-fieldname="address">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Address</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Small Text" data-fieldname="address" placeholder="" data-doctype="Website Settings" style="height: 150px; max-height: 8rem;"></textarea></div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve; max-height: 8rem; overflow: auto;"></div>
                                      <p class="help-box small text-muted">Address and other legal information you may want to put in the footer.</p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">address</span>
                                </div>
                                <div class="frappe-control" data-fieldtype="Small Text" data-fieldname="footer_powered">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Footer "Powered By"</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Small Text" data-fieldname="footer_powered" placeholder="" data-doctype="Website Settings" style="height: 150px; max-height: 2rem;"></textarea></div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve; max-height: 2rem; overflow: auto;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">footer_powered</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="custom_footer_section">
                          <div class="section-head">
                            Custom Footer
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_14">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="footer_template">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Footer Template</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="link-field ui-front" style="position: relative;">
                                          <div class="awesomplete">
                                            <input type="text" class="input-with-feedback form-control" data-target="Web Template" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_4" role="combobox">
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
                                  <span class="tooltip-content">footer_template</span>
                                </div>
                                <div class="frappe-control hide-control" data-fieldtype="Code" data-fieldname="footer_template_values">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;"></label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"></div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">footer_template_values</span>
                                </div>
                                <div class="frappe-control input-max-width hide-control" data-fieldtype="Button" data-fieldname="edit_footer_template_values">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label hide" style="padding-right: 0px;">&nbsp;</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><button class="btn btn-xs btn-default" data-fieldtype="Button" data-fieldname="edit_footer_template_values" placeholder="" data-doctype="Website Settings" value="">Edit Values</button></div>
                                      <div class="hide" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">edit_footer_template_values</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Integrations">
                      <div class="tab-pane fade show" id="website-settings-integrations" role="tabpanel" aria-labelledby="website-settings-integrations-tab">
                        <div class="row form-section card-section empty-section" data-fieldname="__section_15">
                          <div class="section-body"></div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="analytics_section">
                          <div class="section-head">
                            Analytics
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_16">
                              <form>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_view_tracking">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_view_tracking" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Enable in-app website tracking</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">enable_view_tracking</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_google_indexing">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_google_indexing" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Enable Google indexing</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted">To use Google Indexing, enable <a href="/app/google-settings">Google Settings</a>.</p>
                                  </div>
                                  <span class="tooltip-content">enable_google_indexing</span>
                                </div>
                                <div class="frappe-control input-max-width hide-control" data-fieldtype="Button" data-fieldname="authorize_api_indexing_access">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label hide" style="padding-right: 0px;">&nbsp;</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><button class="btn btn-xs btn-default" data-fieldtype="Button" data-fieldname="authorize_api_indexing_access" placeholder="" data-doctype="Website Settings" value="">Authorize API Indexing  Access</button></div>
                                      <div class="hide" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">authorize_api_indexing_access</span>
                                </div>
                                <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="indexing_refresh_token">
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
                                  <span class="tooltip-content">indexing_refresh_token</span>
                                </div>
                                <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="indexing_authorization_code">
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
                                  <span class="tooltip-content">indexing_authorization_code</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="column_break_17">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="google_analytics_id">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Google Analytics ID</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">google_analytics_id</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="google_analytics_anonymize_ip">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="google_analytics_anonymize_ip" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                    <span class="label-area">Google Analytics anonymise IP</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">google_analytics_anonymize_ip</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="account_deletion_settings_section">
                          <div class="section-head">
                            Account Deletion Settings
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_17">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="auto_account_deletion">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Automatically delete account within (hours)</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="auto_account_deletion" placeholder="" data-doctype="Website Settings"></div>
                                      <div class="control-value like-disabled-input" style="display: none;">72</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">auto_account_deletion</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_account_deletion_link">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_account_deletion_link" placeholder="" data-doctype="Website Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Show account deletion link in My Account page</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">show_account_deletion_link</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Header, Robots">
                      <div class="tab-pane fade show" id="website-settings-section_break_38" role="tabpanel" aria-labelledby="website-settings-section_break_38-tab">
                        <div class="row form-section card-section visible-section" data-fieldname="__section_18">
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_18">
                              <form>
                                <div class="frappe-control hide-control" data-fieldtype="Small Text" data-fieldname="subdomain">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;"></label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"></div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">subdomain</span>
                                </div>
                                <div class="frappe-control" data-fieldtype="Code" data-fieldname="head_html">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">&lt;head&gt; HTML</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="ace-editor-target border rounded ace_editor ace_hidpi ace-tomorrow" style="height: 300px;">
                                          <textarea class="ace_text-input" wrap="off" autocorrect="off" autocapitalize="off" spellcheck="false" aria-haspopup="false" aria-autocomplete="both" role="textbox" style="opacity: 0; font-size: 1px;"></textarea>
                                          <div class="ace_gutter" aria-hidden="true">
                                            <div class="ace_layer ace_gutter-layer ace_folding-enabled" style="height: 1e+06px;"></div>
                                          </div>
                                          <div class="ace_scroller" style="line-height: 0px;">
                                            <div class="ace_content">
                                              <div class="ace_layer ace_print-margin-layer">
                                                <div class="ace_print-margin" style="left: 4px; visibility: hidden;"></div>
                                              </div>
                                              <div class="ace_layer ace_marker-layer"></div>
                                              <div class="ace_layer ace_text-layer" style="height: 1e+06px; margin: 0px 4px;"></div>
                                              <div class="ace_layer ace_marker-layer"></div>
                                              <div class="ace_layer ace_cursor-layer ace_hidden-cursors">
                                                <div class="ace_cursor"></div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="ace_scrollbar ace_scrollbar-v" style="display: none; width: 20px;">
                                            <div class="ace_scrollbar-inner" style="width: 20px;">&nbsp;</div>
                                          </div>
                                          <div class="ace_scrollbar ace_scrollbar-h" style="display: none; height: 20px;">
                                            <div class="ace_scrollbar-inner" style="height: 20px;">&nbsp;</div>
                                          </div>
                                          <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: hidden;">
                                            <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;"></div>
                                            <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                        <pre></pre>
                                      </div>
                                      <p class="help-box small text-muted">Added HTML in the &lt;head&gt; section of the web page, primarily used for website verification and SEO</p>
                                      <button class="btn btn-xs btn-default">Expand</button>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">head_html</span>
                                </div>
                                <div class="frappe-control" data-fieldtype="Code" data-fieldname="robots_txt">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Robots.txt</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input">
                                        <div class="ace-editor-target border rounded ace_editor ace_hidpi ace-tomorrow" style="height: 300px;">
                                          <textarea class="ace_text-input" wrap="off" autocorrect="off" autocapitalize="off" spellcheck="false" aria-haspopup="false" aria-autocomplete="both" role="textbox" style="opacity: 0; font-size: 1px;"></textarea>
                                          <div class="ace_gutter" aria-hidden="true">
                                            <div class="ace_layer ace_gutter-layer ace_folding-enabled" style="height: 1e+06px;"></div>
                                          </div>
                                          <div class="ace_scroller" style="line-height: 0px;">
                                            <div class="ace_content">
                                              <div class="ace_layer ace_print-margin-layer">
                                                <div class="ace_print-margin" style="left: 4px; visibility: hidden;"></div>
                                              </div>
                                              <div class="ace_layer ace_marker-layer"></div>
                                              <div class="ace_layer ace_text-layer" style="height: 1e+06px; margin: 0px 4px;"></div>
                                              <div class="ace_layer ace_marker-layer"></div>
                                              <div class="ace_layer ace_cursor-layer ace_hidden-cursors">
                                                <div class="ace_cursor"></div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="ace_scrollbar ace_scrollbar-v" style="display: none; width: 20px;">
                                            <div class="ace_scrollbar-inner" style="width: 20px;">&nbsp;</div>
                                          </div>
                                          <div class="ace_scrollbar ace_scrollbar-h" style="display: none; height: 20px;">
                                            <div class="ace_scrollbar-inner" style="height: 20px;">&nbsp;</div>
                                          </div>
                                          <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: hidden;">
                                            <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;"></div>
                                            <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                        <pre></pre>
                                      </div>
                                      <p class="help-box small text-muted"></p>
                                      <button class="btn btn-xs btn-default">Expand</button>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">robots_txt</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </b-tab>
                    <b-tab title="Redirects">
                      <div class="tab-pane fade show" id="website-settings-redirects_tab" role="tabpanel" aria-labelledby="website-settings-redirects_tab-tab">
                        <div class="row form-section card-section visible-section" data-fieldname="__section_19">
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_19">
                              <form>
                                <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="route_redirects">
                                  <span class="tooltip-content">route_redirects</span>
                                  <div class="grid-field">
                                    <label class="control-label">Route Redirects</label>
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
                                              <div class="col grid-static-col col-xs-5  grid-overflow-no-ellipsis" data-fieldname="source" data-fieldtype="Small Text" title="Source">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis reqd">Source</div>
                                              </div>
                                              <div class="col grid-static-col col-xs-5  grid-overflow-no-ellipsis" data-fieldname="target" data-fieldtype="Small Text" title="Target">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis reqd">Target</div>
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