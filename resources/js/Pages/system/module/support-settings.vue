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
    <Head title="Support Settings" />
    <PageHeader title="Support Settings" pageTitle="Settings" subTitle="Module"/>
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
                              <div id="heatmap-support_settings" class="heatmap"></div>
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
                        <div class="row form-section card-section visible-section" data-fieldname="sb_00">
                          <div class="section-head">
                            Service Level Agreements
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_2">
                              <form>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="track_service_level_agreement">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="track_service_level_agreement" placeholder="" data-doctype="Support Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Track Service Level Agreement</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">track_service_level_agreement</span>
                                </div>
                                <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check" data-fieldname="allow_resetting_service_level_agreement">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_resetting_service_level_agreement" placeholder="" data-doctype="Support Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Allow Resetting Service Level Agreement</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">allow_resetting_service_level_agreement</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="issues_sb">
                          <div class="section-head">
                            Issues
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_3">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="close_issue_after_days">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Close Issue After Days</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Int" data-fieldname="close_issue_after_days" placeholder="" data-doctype="Support Settings"></div>
                                      <div class="control-value like-disabled-input" style="display: none;">7</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">close_issue_after_days</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="portal_sb">
                          <div class="section-head">
                            Support Portal
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_4">
                              <form>
                                <div class="frappe-control" data-fieldtype="Code" data-fieldname="get_started_sections">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Get Started Sections</label>
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
                                            <div class="ace_content" style="transform: translate(0px, 0px); width: 960px; height: 330px;">
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
                                          <div class="ace_scrollbar ace_scrollbar-h" style="display: none; height: 20px; left: 41px; right: 0px; width: 960px;">
                                            <div class="ace_scrollbar-inner" style="height: 20px; width: 960px;">&nbsp;</div>
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
                                      <p class="help-box small text-muted"></p>
                                      <button class="btn btn-xs btn-default">Expand</button>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">get_started_sections</span>
                                </div>
                                <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="show_latest_forum_posts">
                                  <div class="checkbox">
                                    <label>
                                    <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="show_latest_forum_posts" placeholder="" data-doctype="Support Settings"></span>
                                    <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                    <span class="label-area">Show Latest Forum Posts</span>
                                    <span class="ml-1 help"></span>
                                    </label>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                  <span class="tooltip-content">show_latest_forum_posts</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section hide-control" data-fieldname="forum_sb">
                          <div class="section-head">
                            Forum Posts
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_5">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="forum_url">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Forum URL</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">forum_url</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="get_latest_query">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Get Latest Query</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">get_latest_query</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="response_key_list">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Response Key List</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">response_key_list</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="column_break_10">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="post_title_key">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Post Title Key</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">post_title_key</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="post_description_key">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Post Description Key</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">post_description_key</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="post_route_key">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Post Route Key</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">post_route_key</span>
                                </div>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="post_route_string">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Post Route String</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;"></div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">post_route_string</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="greetings_section_section">
                          <div class="section-head">
                            Greetings Section
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-6" data-fieldname="__column_6">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="greeting_title">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Greeting Title</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;">We're here to help</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">greeting_title</span>
                                </div>
                              </form>
                            </div>
                            <div class="form-column col-sm-6" data-fieldname="column_break_19">
                              <form>
                                <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="greeting_subtitle">
                                  <div class="form-group">
                                    <div class="clearfix">
                                      <label class="control-label" style="padding-right: 0px;">Greeting Subtitle</label>
                                      <span class="help"></span>
                                    </div>
                                    <div class="control-input-wrapper">
                                      <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control"></div>
                                      <div class="control-value like-disabled-input" style="display: none;">Browse help topics</div>
                                      <p class="help-box small text-muted"></p>
                                    </div>
                                  </div>
                                  <span class="tooltip-content">greeting_subtitle</span>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <div class="row form-section card-section visible-section" data-fieldname="search_apis_sb">
                          <div class="section-head">
                            Search APIs
                            <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                          </div>
                          <div class="section-body">
                            <div class="form-column col-sm-12" data-fieldname="__column_7">
                              <form>
                                <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="search_apis">
                                  <span class="tooltip-content">search_apis</span>
                                  <div class="grid-field">
                                    <label class="control-label">Search APIs</label>
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
                                              <div class="col grid-static-col col-xs-5 " data-fieldname="source_name" data-fieldtype="Data" title="Source Name">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis">Source Name</div>
                                              </div>
                                              <div class="col grid-static-col col-xs-5 " data-fieldname="source_type" data-fieldtype="Select" title="Source Type">
                                                <div class="field-area" style="display: none;"></div>
                                                <div class="static-area ellipsis reqd">Source Type</div>
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