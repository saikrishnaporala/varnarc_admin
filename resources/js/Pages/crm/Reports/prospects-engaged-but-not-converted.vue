<script lang="ts" setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Swal from "sweetalert2";

import Lottie from "@/Components/widgets/lottie.vue";
import animationData from "@/Components/widgets/msoeawqm.json";
import axios from 'axios';
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import multi_user_image from "@assets/images/users/multi-user.jpg";
import { companies as companiesData } from "@/common/data";

// Data & component imports
const companies = ref(companiesData);
const addCompanyModal = ref(false);
const value = ref(null);
const page = ref(1);
const perPage = ref(8);
const pages = ref<number[]>([]);
const searchQuery = ref<string | null>(null);
const submitted = ref(false);
const dataEdit = ref(false);
const deleteModal = ref(false);
const multi_user_image_src = multi_user_image;

// Default animation options
const defaultOptions = { animationData: animationData };
const defaultOptions1 = { animationData: animationData1 };

// Event object with types
interface Event {
  id: string;
  img_src: string;
  name: string;
  owner: string;
  industry_type: string;
  company_type: string;
  star_value: string;
  location: string;
  employee: string;
  website: string;
  contact_email: string;
  contact_phone: string;
  since: string;
}

const event = ref<Event>({
  id: "",
  img_src: "",
  name: "",
  owner: "",
  industry_type: "",
  company_type: "",
  star_value: "",
  location: "",
  employee: "",
  website: "",
  contact_email: "",
  contact_phone: "",
  since: "",
});

// Computed properties
const displayedPosts = computed(() => paginate(companies.value));
const resultQuery = computed(() => {
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase();
    return displayedPosts.value.filter((data) => {
      return (
        data.contact_email.toLowerCase().includes(search) ||
        data.employee.toLowerCase().includes(search) ||
        data.owner.toLowerCase().includes(search) ||
        data.industry_type.toLowerCase().includes(search) ||
        data.website.toLowerCase().includes(search) ||
        data.star_value.toString().toLowerCase().includes(search) ||
        data.name.toLowerCase().includes(search) ||
        data.location.toLowerCase().includes(search)
      );
    });
  } else {
    return displayedPosts.value;
  }
});

// Methods
const handleSubmit = async () => {
  submitted.value = true;
  const isValid =
    event.value.name &&
    event.value.owner &&
    event.value.industry_type &&
    event.value.company_type &&
    event.value.star_value &&
    event.value.location &&
    event.value.employee &&
    event.value.website &&
    event.value.contact_email &&
    event.value.contact_phone &&
    event.value.since;
  if (isValid) {
    if (dataEdit.value) {
      addCompanyModal.value = false;
      companies.value = companies.value.map((item) =>
        item.id.toString() == event.value.id.toString()
          ? { ...item, ...event.value }
          : item
      );
      try {
        const response = await axios.post('/api/companies/store', event.value);
        console.log('Company updated successfully:', response.data);
      } catch (error) {
        console.error('Error submitting company:', error);
      }
    } else {
      const newCompany = {
        id: (Math.floor(Math.random() * 100 + 20) - 20).toString(),
        img_src: multi_user_image_src,
        ...event.value,
      };
      try {
        const response = await axios.post('/api/companies/store', newCompany);
        console.log('Company submitted successfully:', response.data);
      } catch (error) {
        console.error('Error submitting company:', error);
      }
      companies.value = [newCompany, ...companies.value];
      addCompanyModal.value = false;
    }
  }
};

const onSort = (column: string) => {
  const direction = ref('asc');
  direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  const sortedArray = [...companies.value];
  sortedArray.sort((a, b) => {
    const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
    return direction.value === 'asc' ? res : -res;
  });
  companies.value = sortedArray;
};

const editDetails = (data: Event) => {
  addCompanyModal.value = true;
  dataEdit.value = true;
  event.value = {
    id: data.id,
    img_src: data.img_src,
    name: data.name,
    owner: data.owner,
    industry_type: data.industry_type,
    company_type: data.company_type,
    star_value: data.star_value,
    location: data.location,
    employee: data.employee,
    website: data.website,
    contact_email: data.contact_email,
    contact_phone: data.contact_phone,
    since: data.since,
  };
  submitted.value = false;
};

const toggleModal = () => {
  addCompanyModal.value = true;
  dataEdit.value = false;
  event.value = {
    id: "",
    img_src: "",
    name: "",
    owner: "",
    industry_type: "",
    company_type: "",
    star_value: "",
    location: "",
    employee: "",
    website: "",
    contact_email: "",
    contact_phone: "",
    since: "",
  };
  submitted.value = false;
};

const deleteModalToggle = (data: Event) => {
  deleteModal.value = true;
  event.value.id = data.id;
};

const deleteData = async () => {
  if (event.value.id) {
    companies.value = companies.value.filter((item: { id: string; }) => item.id !== event.value.id);
    deleteModal.value = false;
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.delete(`/api/companies/${event.value.id}`)
      console.log(response);
      companies.value = response.data.result;
    } catch (err) {
      error.value = 'Failed to fetch leads data.';
      console.error(err);
    } finally {
      loading.value = false;
    }
  }
};

// Pagination methods
const setPages = () => {
  const numberOfPages = Math.ceil(companies.value.length / perPage.value);
  pages.value = [];
  for (let index = 1; index <= numberOfPages; index++) {
    pages.value.push(index);
  }
};

const paginate = (items: any[]) => {
  const from = page.value * perPage.value - perPage.value;
  const to = page.value * perPage.value;
  return items.slice(from, to);
};

// Watchers
watch(companies, setPages);

// Lifecycle hooks
onMounted(() => {
  fetchCompanies();
  // setPages();
});

const loading = ref(false);
const error = ref<string | null>(null);

const fetchCompanies = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/companies');  // Adjust the API endpoint as needed
    console.log(response);
    companies.value = response.data.result;
  } catch (err) {
    error.value = 'Failed to fetch companies data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};
const getImageSrc = (img: string | null) => {
  // If leadImage exists, use it, otherwise use the default image
  console.log(new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/avatar-1.jpg");
  return img ? new URL(`@/images/users/${img}`, import.meta.url).href : new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/users/avatar-1.jpg";
};

</script>

<template>
  <Layout>

    <Head title="Home" />
    <PageHeader title="Home" pageTitle="CRM" />
    <BRow>
      <BCol lg="12">
        <BCard no-body>
          <BCardHeader>
            <div class="d-flex align-items-center flex-wrap gap-2">
              <div class="flex-grow-1">
                <BButton variant="info" class="add-btn me-1" @click="toggleModal">
                  <i class="ri-add-fill me-1 align-bottom"></i> Add Company
                </BButton>
              </div>
              <div class="flex-shrink-0">
                <div class="hstack text-nowrap gap-2">
                  <BButton variant="soft-danger" class="material-shadow-none" id="remove-actions" @click="deleteMultiple">
                    <i class="ri-delete-bin-2-line"></i>
                  </BButton>
                  <BButton variant="danger" class="material-shadow-none">
                    <i class="ri-filter-2-line me-1 align-bottom"></i> Filters
                  </BButton>
                  <BButton variant="soft-success" class="material-shadow-none">Import</BButton>
                  <BDropdown variant="link" class="card-header-dropdown" toggle-class="btn btn-soft-info material-shadow-none" no-caret menu-class="dropdown-menu-end" :offset="{ alignmentAxis: -105, crossAxis: 0, mainAxis: 10 }">
                    <template #button-content><i class="ri-more-2-fill"></i></template>
                    <BDropdownItem>All</BDropdownItem>
                    <BDropdownItem>Last Week</BDropdownItem>
                    <BDropdownItem>Last Month</BDropdownItem>
                    <BDropdownItem>Last Year</BDropdownItem>
                  </BDropdown>
                  <BButton variant="soft-danger" class="material-shadow-none">Discard</BButton>
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
                        <button class="btn btn-primary btn-sm primary-action" data-label="Save" data-working-label="Saving"><span>S<span class="alt-underline">a</span>ve</span></button>     
                </div>
              </div>
            </div>
          </BCardHeader>
        </BCard>
      </BCol>
      <BCol xxl="12">
        <BCard no-body id="companyList">
          <BCardHeader>
            <BRow class="g-2">
              <BCol md="3">
                <div class="search-box">
                  <input type="text" class="form-control search" placeholder="Search for company..." v-model="searchQuery" />
                  <i class="ri-search-line search-icon"></i>
                </div>
              </BCol>
              <BCol md="auto" class="ms-auto">
                <div class="d-flex align-items-center gap-2">
                  <span class="text-muted flex-shrink-0">Sort by: </span>

                  <Multiselect class="form-control" style="width:150px;" v-model="value" :close-on-select="true" :searchable="true" :create-option="true" :options="[
                    { value: 'Owner', label: 'Owner' },
                    { value: 'Company', label: 'Company' },
                    { value: 'location', label: 'Location' },
                  ]" />
                </div>
              </BCol>
            </BRow>
          </BCardHeader>
          <BCardBody>
            <div class="layout-main-section">
              <div class="page-form row hide"></div>
              <div class="editor-js-container">
                <div id="editorjs" class="desk-page page-main-content">
                  <div class="codex-editor">
                    <div class="codex-editor__redactor" style="padding-bottom: 300px;">
                      <div class="ce-block col-xs-12">
                        <div class="ce-block__content">
                          <div chart_name="Territory Wise Sales">
                            <div class="widget dashboard-widget-box" data-widget-name="j5kecv3eon">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title" title="Territory Wise Sales"><span class="ellipsis" title="Territory Wise Sales">Territory Wise Sales</span></div>
                                  <div class="widget-subtitle">Last synced <span class="frappe-timestamp " data-timestamp="2024-11-26 00:00:03.246066" title="26-11-2024 00:00:03">24 minutes ago</span></div>
                                </div>
                                <div class="widget-control">
                                  <div class="chart-actions dropdown pull-right">
                                    <button data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-xs btn-secondary chart-menu">
                                      <svg class="icon icon-sm">
                                        <use href="#icon-dot-horizontal">
                                        </use>
                                      </svg>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                      <li><a class="dropdown-item" data-action="action-refresh">Refresh</a></li>
                                      <li><a class="dropdown-item" data-action="action-edit">Edit</a></li>
                                      <li><a class="dropdown-item" data-action="action-reset">Reset Chart</a></li>
                                      <li><a class="dropdown-item" data-action="action-list">Opportunity List</a></li>
                                    </ul>
                                  </div>
                                  <div class="filter-chart btn btn-xs pull-right" data-original-title="" title="">
                                    <svg class="icon  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#icon-filter"></use>
                                    </svg>
                                  </div>
                                </div>
                              </div>
                              <div class="widget-body">
                                <div class="chart-loading-state text-muted" style="height: 240px; display: none;">Loading...</div>
                                <div class="chart-loading-state text-muted" style="height: 240px;">No Data</div>
                                <div style="display: none;"></div>
                              </div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12">
                        <div class="ce-block__content">
                          <div class="widget spacer"></div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12">
                        <div class="ce-block__content">
                          <div class="ce-header" data-placeholder=""><span class="h4"><b>Your Shortcuts</b></span></div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="ce-block__content">
                          <div shortcut_name="Lead">
                            <div class="widget shortcut-widget-box" data-widget-name="2nljlt7mld" role="link" tabindex="0" aria-label="Lead">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Lead">Lead</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control">
                                  <div class="indicator-pill no-indicator-dot ellipsis gray">0 Open</div>
                                  <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                    <use class="" href="#es-line-arrow-up-right"></use>
                                  </svg>
                                </div>
                              </div>
                              <div class="widget-body"></div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="ce-block__content">
                          <div shortcut_name="Opportunity">
                            <div class="widget shortcut-widget-box" data-widget-name="7ghrkdm94i" role="link" tabindex="0" aria-label="Opportunity">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Opportunity">Opportunity</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control">
                                  <div class="indicator-pill no-indicator-dot ellipsis gray">0 Assigned</div>
                                  <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                    <use class="" href="#es-line-arrow-up-right"></use>
                                  </svg>
                                </div>
                              </div>
                              <div class="widget-body"></div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="ce-block__content">
                          <div shortcut_name="Customer">
                            <div class="widget shortcut-widget-box" data-widget-name="nn69vvp0df" role="link" tabindex="0" aria-label="Customer">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Customer">Customer</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control">
                                  <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                    <use class="" href="#es-line-arrow-up-right"></use>
                                  </svg>
                                </div>
                              </div>
                              <div class="widget-body"></div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="ce-block__content">
                          <div shortcut_name="Sales Analytics">
                            <div class="widget shortcut-widget-box" data-widget-name="80paj1dda2" role="link" tabindex="0" aria-label="Sales Analytics">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Sales Analytics">Sales Analytics</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control">
                                  <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                    <use class="" href="#es-line-arrow-up-right"></use>
                                  </svg>
                                </div>
                              </div>
                              <div class="widget-body"></div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="ce-block__content">
                          <div shortcut_name="Dashboard">
                            <div class="widget shortcut-widget-box" data-widget-name="m72ir1lam5" role="link" tabindex="0" aria-label="Dashboard">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Dashboard">Dashboard</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control">
                                  <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                    <use class="" href="#es-line-arrow-up-right"></use>
                                  </svg>
                                </div>
                              </div>
                              <div class="widget-body"></div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12">
                        <div class="ce-block__content">
                          <div class="widget spacer"></div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12">
                        <div class="ce-block__content">
                          <div class="ce-header" data-placeholder=""><span class="h4"><b>Reports &amp; Masters</b></span></div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                        <div class="ce-block__content">
                          <div card_name="Sales Pipeline">
                            <div class="widget links-widget-box" data-widget-name="qgbf3pqlai">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Sales Pipeline">Sales Pipeline</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control"></div>
                              </div>
                              <div class="widget-body">
                                <a href="/app/lead" class="link-item ellipsis
                                  onboard-spotlight " type="Link" title="Lead">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Lead</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/opportunity" class="link-item ellipsis
                                  onboard-spotlight " type="Link" title="Opportunity">
                                  <span class="link-content ellipsis">
                                    <span class="link-text" data-original-title="" title="">Opportunity</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/customer" class="link-item ellipsis
                                  onboard-spotlight " type="Link" title="Customer">
                                  <span class="link-content ellipsis">
                                    <span class="link-text" data-original-title="" title="">Customer</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/contract" class="link-item ellipsis
                                  " type="Link" title="Contract">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Contract</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/appointment" class="link-item ellipsis
                                  " type="Link" title="Appointment">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Appointment</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/newsletter" class="link-item ellipsis
                                  " type="Link" title="Newsletter">
                                  <span class="link-content ellipsis">
                                    <span class="link-text" data-original-title="" title="">Newsletter</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/communication" class="link-item ellipsis
                                  " type="Link" title="Communication">
                                  <span class="link-content ellipsis">
                                    <span class="link-text" data-original-title="" title="">Communication</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                              </div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                        <div class="ce-block__content">
                          <div card_name="Masters">
                            <div class="widget links-widget-box" data-widget-name="imb9pgves6">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Masters">Masters</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control"></div>
                              </div>
                              <div class="widget-body">
                                <a href="/app/territory" class="link-item ellipsis
                                  " type="Link" title="Territory">
                                  <span class="link-content ellipsis">
                                    <span class="link-text" data-original-title="" title="">Territory</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/customer-group" class="link-item ellipsis
                                  " type="Link" title="Customer Group">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Customer Group</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/contact" class="link-item ellipsis
                                  " type="Link" title="Contact">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Contact</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/prospect" class="link-item ellipsis
                                  " type="Link" title="Prospect">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Prospect</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/sales-person" class="link-item ellipsis
                                  " type="Link" title="Sales Person">
                                  <span class="link-content ellipsis">
                                    <span class="link-text" data-original-title="" title="">Sales Person</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/sales-stage" class="link-item ellipsis
                                  " type="Link" title="Sales Stage">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Sales Stage</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/lead-source" class="link-item ellipsis
                                  " type="Link" title="Lead Source">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Lead Source</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                              </div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                        <div class="ce-block__content">
                          <div card_name="Reports">
                            <div class="widget links-widget-box" data-widget-name="r4s6aootrh">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Reports">Reports</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control"></div>
                              </div>
                              <div class="widget-body">
                                <a href="/app/query-report/Lead Details" class="link-item ellipsis
                                  onboard-spotlight disabled-link" type="Link" title="Lead Details">
                                  <span class="link-content ellipsis disabled-link">Lead Details</span>
                                  <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                    <div class="arrow"></div>
                                    <h3 class="popover-title" style="display: none;"></h3>
                                    <div class="popover-content" style="padding: 12px;">
                                      <div class="small text-muted">You need to create these first: </div>
                                      <div class="small">Lead</div>
                                    </div>
                                  </div>
                                </a>
                                <a href="/app/query-report/Sales Pipeline Analytics" class="link-item ellipsis
                                  " type="Link" title="Sales Pipeline Analytics">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Sales Pipeline Analytics</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/query-report/Opportunity Summary by Sales Stage" class="link-item ellipsis
                                  " type="Link" title="Opportunity Summary by Sales Stage">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Opportunity Summary by Sales Stage</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/sales-funnel" class="link-item ellipsis
                                  onboard-spotlight " type="Link" title="Sales Funnel">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Sales Funnel</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/query-report/Prospects Engaged But Not Converted" class="link-item ellipsis
                                  onboard-spotlight disabled-link" type="Link" title="Prospects Engaged But Not Converted">
                                  <span class="link-content ellipsis disabled-link">Prospects Engaged But Not Converted</span>
                                  <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                    <div class="arrow"></div>
                                    <h3 class="popover-title" style="display: none;"></h3>
                                    <div class="popover-content" style="padding: 12px;">
                                      <div class="small text-muted">You need to create these first: </div>
                                      <div class="small">Lead</div>
                                    </div>
                                  </div>
                                </a>
                                <a href="/app/query-report/First Response Time for Opportunity" class="link-item ellipsis
                                  disabled-link" type="Link" title="First Response Time for Opportunity">
                                  <span class="link-content ellipsis disabled-link">First Response Time for Opportunity</span>
                                  <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                    <div class="arrow"></div>
                                    <h3 class="popover-title" style="display: none;"></h3>
                                    <div class="popover-content" style="padding: 12px;">
                                      <div class="small text-muted">You need to create these first: </div>
                                      <div class="small">Opportunity</div>
                                    </div>
                                  </div>
                                </a>
                                <a href="/app/query-report/Inactive Customers" class="link-item ellipsis
                                  " type="Link" title="Inactive Customers">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Inactive Customers</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/query-report/Campaign Efficiency" class="link-item ellipsis
                                  disabled-link" type="Link" title="Campaign Efficiency">
                                  <span class="link-content ellipsis disabled-link">Campaign Efficiency</span>
                                  <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                    <div class="arrow"></div>
                                    <h3 class="popover-title" style="display: none;"></h3>
                                    <div class="popover-content" style="padding: 12px;">
                                      <div class="small text-muted">You need to create these first: </div>
                                      <div class="small">Lead</div>
                                    </div>
                                  </div>
                                </a>
                                <a href="/app/query-report/Lead Owner Efficiency" class="link-item ellipsis
                                  disabled-link" type="Link" title="Lead Owner Efficiency">
                                  <span class="link-content ellipsis disabled-link">Lead Owner Efficiency</span>
                                  <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                    <div class="arrow"></div>
                                    <h3 class="popover-title" style="display: none;"></h3>
                                    <div class="popover-content" style="padding: 12px;">
                                      <div class="small text-muted">You need to create these first: </div>
                                      <div class="small">Lead</div>
                                    </div>
                                  </div>
                                </a>
                              </div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                        <div class="ce-block__content">
                          <div card_name="Campaign">
                            <div class="widget links-widget-box" data-widget-name="gulb5saf79">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Campaign">Campaign</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control"></div>
                              </div>
                              <div class="widget-body">
                                <a href="/app/campaign" class="link-item ellipsis
                                  " type="Link" title="Campaign">
                                  <span class="link-content ellipsis">
                                    <span class="link-text" data-original-title="" title="">Campaign</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/email-campaign" class="link-item ellipsis
                                  " type="Link" title="Email Campaign">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Email Campaign</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/sms-center" class="link-item ellipsis
                                  " type="Link" title="SMS Center">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">SMS Center</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/sms-log" class="link-item ellipsis
                                  " type="Link" title="SMS Log">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">SMS Log</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/email-group" class="link-item ellipsis
                                  " type="Link" title="Email Group">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Email Group</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                              </div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                        <div class="ce-block__content">
                          <div card_name="Settings">
                            <div class="widget links-widget-box" data-widget-name="hcvhd050hr">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Settings">Settings</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control"></div>
                              </div>
                              <div class="widget-body">
                                <a href="/app/crm-settings" class="link-item ellipsis
                                  " type="Link" title="CRM Settings">
                                  <span class="link-content ellipsis">
                                    <span class="link-text" data-original-title="" title="">CRM Settings</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/sms-settings" class="link-item ellipsis
                                  " type="Link" title="SMS Settings">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">SMS Settings</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                              </div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                        <div class="ce-block__content">
                          <div card_name="Maintenance">
                            <div class="widget links-widget-box" data-widget-name="ss68s0h23i">
                              <div class="widget-head">
                                <div class="widget-label">
                                  <div class="widget-title"><span class="ellipsis" title="Maintenance">Maintenance</span></div>
                                  <div class="widget-subtitle"></div>
                                </div>
                                <div class="widget-control"></div>
                              </div>
                              <div class="widget-body">
                                <a href="/app/maintenance-schedule" class="link-item ellipsis
                                  onboard-spotlight " type="Link" title="Maintenance Schedule">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Maintenance Schedule</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/maintenance-visit" class="link-item ellipsis
                                  " type="Link" title="Maintenance Visit">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Maintenance Visit</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                                <a href="/app/warranty-claim" class="link-item ellipsis
                                  " type="Link" title="Warranty Claim">
                                  <span class="link-content ellipsis">
                                    <span class="link-text">Warranty Claim</span>
                                    <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-arrow-up-right"></use>
                                    </svg>
                                  </span>
                                </a>
                              </div>
                              <div class="widget-footer"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="codex-editor-overlay">
                      <div class="codex-editor-overlay__container">
                        <div class="codex-editor-overlay__rectangle" style="display: none;"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="workspace-footer">
                <button data-label="New" class="btn btn-default ellipsis btn-new-workspace">
                  <svg class="es-icon es-line icon-xs" style="" aria-hidden="true">
                    <use class="" href="#es-line-add"></use>
                  </svg>
                  <span class="hidden-xs" data-label="New">New</span>
                </button>
                <button class="btn btn-default btn-sm mr-2 btn-edit-workspace" data-label="Edit">
                  <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                    <use class="" href="#es-line-edit"></use>
                  </svg>
                  <span class="hidden-xs" data-label="Edit">Edit</span>
                </button>
              </div>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>

    <BModal v-model="addCompanyModal" id="showmodal" hide-footer title-class="exampleModalLabel" header-class="bg-info-subtle p-3" class="v-modal-custom" centered size="lg" :title="dataEdit ? 'Edit Company' : 'Add Company'">
      <BForm id="addform" class="tablelist-form" autocomplete="off">
        <input type="hidden" id="id">
        <BRow class="g-3">
          <BCol lg="12">
            <div class="text-center">
              <div class="position-relative d-inline-block">
                <div class="position-absolute bottom-0 end-0">
                  <label for="company-logo-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Select Image">
                    <div class="avatar-xs cursor-pointer">
                      <div class="avatar-title bg-light border rounded-circle text-muted">
                        <i class="ri-image-fill"></i>
                      </div>
                    </div>
                  </label>
                  <input class="form-control d-none" value="" id="company-logo-input" type="file" accept="image/png, image/gif, image/jpeg">
                </div>
                <div class="avatar-lg p-1">
                  <div class="avatar-title bg-light rounded-circle">
                    <img :src="event.img_src || multi_user_image" id="companylogo-img" class="avatar-md rounded-circle object-fit-cover">
                  </div>
                </div>
              </div>
              <h5 class="fs-13 mt-3">Company Logo</h5>
            </div>
            <div>
              <label for="companyname" class="form-label">Name</label>
              <input type="text" id="companyname" class="form-control" placeholder="Enter company name" v-model="event.name" :class="{ 'is-invalid': submitted && !event.name }">
              <div class="invalid-feedback">Please enter a company name.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="ownername" class="form-label">Owner Name</label>
              <input type="text" id="ownername" class="form-control" placeholder="Enter owner name" v-model="event.owner" :class="{ 'is-invalid': submitted && !event.owner }">
              <div class="invalid-feedback">Please enter a owner name.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="industrytype" class="form-label">Industry Type</label>
              <select class="form-select" id="industrytype" v-model="event.industry_type" :class="{ 'is-invalid': submitted && !event.industry_type }">
                <option value="">Select industry type</option>
                <option value="Computer Industry">Computer Industry</option>
                <option value="Chemical Industries">Chemical Industries</option>
                <option value="Health Services">Health Services</option>
                <option value="Telecommunications Services">Telecommunications Services</option>
                <option value="Textiles: Clothing, Footwear">Textiles: Clothing, Footwear</option>
              </select>
              <div class="invalid-feedback">Please select a industry type.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="companytype" class="form-label">Company Type</label>
              <select class="form-select" id="companytype" v-model="event.company_type" :class="{ 'is-invalid': submitted && !event.company_type }">
                <option value="">Select company type</option>
                <option value="Supplier">Supplier</option>
                <option value="Buyer">Buyer</option>
              </select>
              <div class="invalid-feedback">Please select a company type.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="starvalue" class="form-label">Rating</label>
              <input type="text" id="starvalue" class="form-control" placeholder="Enter rating" v-model="event.star_value" :class="{ 'is-invalid': submitted && !event.star_value }">
              <div class="invalid-feedback">Please enter a rating.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="location" class="form-label">Location</label>
              <input type="text" id="location" class="form-control" placeholder="Enter location" v-model="event.location" :class="{ 'is-invalid': submitted && !event.location }">
              <div class="invalid-feedback">Please enter a location.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="employee" class="form-label">Employee</label>
              <input type="text" id="employee" class="form-control" placeholder="Enter employee" v-model="event.employee" :class="{ 'is-invalid': submitted && !event.employee }">
              <div class="invalid-feedback">Please enter a employee.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="website" class="form-label">Website</label>
              <input type="text" id="website" class="form-control" placeholder="Enter website" v-model="event.website" :class="{ 'is-invalid': submitted && !event.website }">
              <div class="invalid-feedback">Please enter a website.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="contactemail" class="form-label">Contact Email</label>
              <input type="text" id="contactemail" class="form-control" placeholder="Enter contact email" v-model="event.contact_email" :class="{ 'is-invalid': submitted && !event.contact_email }">
              <div class="invalid-feedback">Please enter a email.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="contactphone" class="form-label">Contact Phone</label>
              <input type="text" id="contactphone" class="form-control" placeholder="Enter contact phone" v-model="event.contact_phone" :class="{ 'is-invalid': submitted && !event.contact_phone }">
              <div class="invalid-feedback">Please enter a phone.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="since" class="form-label">Since</label>
              <input type="text" id="since" class="form-control" placeholder="Enter since" v-model="event.since" :class="{ 'is-invalid': submitted && !event.since }">
              <div class="invalid-feedback">Please enter a year.</div>
            </div>
          </BCol>
        </BRow>
        <div class="hstack gap-2 justify-content-end mt-3">
          <BButton type="button" variant="light" id="closemodal" @click="addCompanyModal = false">Close</BButton>
          <BButton type="button" variant="success" id="add-btn" @click="handleSubmit">
            {{ dataEdit ? 'Update' : 'Add Company' }}
          </BButton>
        </div>
      </BForm>
    </BModal>

    <!-- delete modal -->
    <BModal v-model="deleteModal" modal-class="zoomIn" hide-footer no-close-on-backdrop centered>
      <div class="mt-2 text-center">
        <lottie class="avatar-xl" colors="primary:#f7b84b,secondary:#f06548" :options="defaultOptions1" :height="100" :width="100" />
        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
          <h4>You are about to delete a contact ?</h4>
          <p class="text-muted mx-4 mb-0">ADeleting your contact will remove all of your information from our database.</p>
        </div>
      </div>
      <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
        <button type="button" class="btn w-sm btn-light material-shadow-none" @click="deleteModal = false">Close</button>
        <button type="button" class="btn w-sm btn-danger" id="delete-record" @click="deleteData">Yes, Delete It!</button>
      </div>
    </BModal>
  </Layout>
</template>