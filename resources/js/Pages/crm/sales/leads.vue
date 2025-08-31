<script lang="ts" setup>
import { ref, computed, onMounted, watch, reactive } from 'vue';
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import Swal from "sweetalert2";
import Lottie from "@/Components/widgets/lottie.vue";
import animationData from "@/Components/widgets/msoeawqm.json";
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import multi_user_image from "@assets/images/users/multi-user.jpg";
import { leads as initialLeads } from "@/common/data";
import axios from 'axios';
import { Console } from 'console';
import LeadForm from "@interfaces/Lead";

const form = reactive<LeadForm>({
  id: null,
  leadImage: null,
  leadsource: null,
  campaign_name: null,
  salutation: null,
  name: null,
  series: null,
  job_title: null,
  phone: null,
  email: null,
  company: null,
  annual_revenue: null,
  industry: null,
  city: null,
  state: null,
  country: null,
  lead_owner: null,
  tags: null,
});

// Validation method
const validateForm = (): boolean => {
  return (
    form.leadsource !== null &&
    form.name !== null &&
    form.phone !== null &&
    form.email !== null &&
    form.company !== null &&
    form.tags !== null
  );
};

// Reset form method
const resetForm = () => {
  submitted.value = false;
  Object.keys(form).forEach((key) => {
    form[key as keyof LeadForm] = null;
  });
};

// Reactive variables
const rightOffcanvas = ref(false);
const addLeadsModal = ref(false);
const timeConfig = ref({
  enableTime: false,
  dateFormat: "d M, Y"
});
const date2 = ref(null);
const defaultOptions = ref({
  animationData: animationData
});
const defaultOptions1 = ref({
  animationData: animationData1
});
const multiUserImage = multi_user_image;
const page = ref(1);
const perPage = ref(8);
const pages = ref<number[]>([]);
const value = ref(null);
const searchQuery = ref<string | null>(null);
const date = ref(null);
const tagvalue = ref(null);
const tagoption = ref([
  { value: 'Lead', label: 'Lead' },
  { value: 'Partner', label: 'Partner' },
  { value: 'Exiting', label: 'Exiting' },
  { value: 'Long-tern', label: 'Long-tern' }
]);

const rangeDateconfig = ref({
  mode: "range",
  dateFormat: "d M, Y"
});
// const leads = ref(initialLeads);
const leads = ref([]);

const submitted = ref(false);
const dataEdit = ref(false);
const deleteModal = ref(false);
const event = ref({
  id: "",
  leadsId: "",
  leadImage: "",
  name: "",
  company: "",
  phone: "",
  email: "",
  score: "",
  tags: [] as string[],
  location: ""
});

// Computed properties
const displayedPosts = computed(() => paginate(leads.value));
const resultQuery = computed(() => {
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase();
    return displayedPosts.value.filter((data) => {
      return (
        data.leadsId.toLowerCase().includes(search) ||
        data.name.toLowerCase().includes(search) ||
        data.company.toLowerCase().includes(search) ||
        data.score.toString().toLowerCase().includes(search) ||
        data.phone.toLowerCase().includes(search) ||
        data.email.toLowerCase().includes(search) ||
        data.location.toLowerCase().includes(search)
      );
    });
  } else {
    return displayedPosts.value;
  }
});

// Watchers
watch(leads, () => {
  setPages();
});

// Methods
const handleSubmit = async () => {
  submitted.value = true;
  if (
    event.value.name &&
    event.value.company &&
    event.value.score &&
    event.value.phone &&
    event.value.email &&
    event.value.location &&
    event.value.tags.length
  ) {
    if (dataEdit.value) {
      leads.value = leads.value.map((item) =>
        item.id.toString() === event.value.id.toString()
          ? { ...item, ...event.value }
          : item
      );
      try {
        // POST request to your API endpoint
        const response = await axios.post('/api/leads/store', event.value);

        // Handle the response if needed
        console.log('Lead updated successfully:', response.data);
      } catch (error) {
        // Handle the error
        console.error('Error submitting lead:', error);
      }
    } else {
      const newLead = {
        id: Math.floor(Math.random() * 100 + 20) - 20,
        leadImage: multiUserImage,
        ...event.value
      };
      try {
        // POST request to your API endpoint
        const response = await axios.post('/api/leads/store', newLead);

        // Handle the response if needed
        console.log('Lead submitted successfully:', response.data);
      } catch (error) {
        // Handle the error
        console.error('Error submitting lead:', error);
      }
      leads.value = [newLead, ...leads.value];
    }
    addLeadsModal.value = false;
  }
};

const onSort = (column: keyof typeof event.value) => {
  const direction = ref("asc");
  direction.value = direction.value === "asc" ? "desc" : "asc";
  leads.value.sort((a, b) => {
    const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
    return direction.value === "asc" ? res : -res;
  });
};

// const editDetails = (data: typeof event.value) => {
//   dataEdit.value = true;
//   addLeadsModal.value = true;
//   event.value = { ...data };
//   submitted.value = false;
// };

const editDetails = async (data: any) => {
  // addLeadsModal.value = true;
  dataEdit.value = true;
  console.log(data);
  if (event.value.id) {
    loading.value = true;
    error.value = null;
    // try {
    //   const response = await axios.get(`/api/leads/${event.value.id}`)
    //   // const response = await axios.post('/api/leads/delete');  // Adjust the API endpoint as needed
    //   console.log(response);
    //   leads.value = response.data.result;
    // } catch (err) {
    //   error.value = 'Failed to fetch leads data.';
    //   console.error(err);
    // } finally {
    //   loading.value = false;
    // }
  }
  event.value = {
    id: data.id,
    leadsId: "",
    leadImage: "",
    name: data.name,
    company: data.company,
    phone: data.phone,
    email: data.email,
    score: data.score,
    tags: data.tags,
    location: data.location
  };
  submitted.value = false;
};

const toggleModal = () => {
  addLeadsModal.value = true;
  dataEdit.value = false;
  event.value = {
    id: "",
    leadsId: "",
    leadImage: "",
    name: "",
    company: "",
    phone: "",
    email:"",
    score: "",
    tags: [],
    location: ""
  };
  submitted.value = false;
};

const deleteModalToggle = (data: typeof event.value) => {
  deleteModal.value = true;
  event.value.id = data.id;
};

const deleteData = async () => {
  if (event.value.id) {
    leads.value = leads.value.filter((item) => item.id !== event.value.id);
    deleteModal.value = false;
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.delete(`/api/leads/${event.value.id}`)
      // const response = await axios.post('/api/leads/delete');  // Adjust the API endpoint as needed
      console.log(response);
      leads.value = response.data.result;
    } catch (err) {
      error.value = 'Failed to fetch leads data.';
      console.error(err);
    } finally {
      loading.value = false;
    }
  }
};

const setPages = () => {
  const numberOfPages = Math.ceil(leads.value.length / perPage.value);
  pages.value = Array.from({ length: numberOfPages }, (_, i) => i + 1);
};

const paginate = (items: typeof leads.value) => {
  const from = (page.value - 1) * perPage.value;
  return items.slice(from, from + perPage.value);
};

onMounted(() => {
  fetchLeadSource();
  fetchLeads();
  const checkAll = document.getElementById("checkAll");
  if (checkAll) {
    checkAll.onclick = () => {
      const checkboxes = document.querySelectorAll(
        '.form-check-all input[type="checkbox"]'
      ) as NodeListOf<HTMLInputElement>;
      const removeActions = document.getElementById("remove-actions");

      checkboxes.forEach((checkbox) => {
        checkbox.checked = checkAll.checked;
        checkbox.closest("tr")?.classList.toggle("table-active", checkAll.checked);
      });

      if (removeActions) {
        removeActions.style.display = checkAll.checked ? "block" : "none";
      }
    };
  }

  const checkboxes = document.querySelectorAll(
    '#leadsList .form-check-input'
  ) as NodeListOf<HTMLInputElement>;

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      const checkedCount = document.querySelectorAll(
        '#leadsList .form-check-input:checked'
      ).length;
      const removeActions = document.getElementById("remove-actions");

      if (removeActions) {
        removeActions.style.display = checkedCount > 0 ? "block" : "none";
      }
    });
  });
});

const loading = ref(false);   // For loading state
const error = ref<string | null>(null);   // For handling errors

// Lead Sources
const leadSources = ref([]);
const fetchLeadSource = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await fetch('/api/lead-sources');
    const data = await response.json();
    leadSources.value = data;
  } catch (err) {
    error.value = 'Failed to fetch leads data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

// Handle changes in the lead source selection
function handleLeadSourceChange() {
  console.log('Selected Lead Source:', form.leadsource.value);
}

const fetchLeads = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/leads');  // Adjust the API endpoint as needed
    console.log(response);
    response.data.result.forEach(lead => {
      if (lead.tags) {
        lead.tags = lead.tags.split(',').map(tag => tag.trim());
      }
    });
    leads.value = response.data.result;
  } catch (err) {
    error.value = 'Failed to fetch leads data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};
const getImageSrc = (leadImage: string | null) => {
  // If leadImage exists, use it, otherwise use the default image
  console.log(new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/avatar-1.jpg");
  return leadImage ? new URL(`@/images/users/${leadImage}`, import.meta.url).href : new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/users/avatar-1.jpg";
};

</script>

<style>
#mobileAuthNavBar {
  min-height: 125px;
  justify-content: space-between;
}
</style>

<template>
  <Layout>
    <Head title="Leads" />
    <PageHeader title="Leads" pageTitle="CRM" />
    <BRow v-if=!dataEdit>
      <BCol lg="12">
        <BCard no-body id="leadsList">
          <BCardHeader class="border-0">
            <BRow class="g-4 align-items-center">
              <BCol sm="3">
                <div class="search-box">
                  <input type="text" class="form-control search" v-model="searchQuery" placeholder="Search for..." />
                  <i class="ri-search-line search-icon"></i>
                </div>
              </BCol>
              <BCol sm="auto" class="ms-auto">
                <div class="hstack gap-2">
                  <BButton variant="soft-danger" id="remove-actions" @click="deleteMultiple">
                    <i class="ri-delete-bin-2-line"></i>
                  </BButton>
                  <BButton @click="rightOffcanvas = true" type="button" variant="info" href="#offcanvasExample">
                    <i class="ri-filter-3-line align-bottom me-1"></i> Fliters
                  </BButton>
                  <BButton type="button" variant="success" class="add-btn" id="create-btn" @click="toggleModal">
                    <i class="ri-add-line align-bottom me-1"></i> Add Leads
                  </BButton>
                  <BDropdown variant="link" class="card-header-dropdown" toggle-class="btn btn-soft-info btn-icon fs-14" no-caret menu-class="dropdown-menu-end">
                    <template #button-content><i class="ri-settings-4-line"></i></template>
                    <BDropdownItem>Copy</BDropdownItem>
                    <BDropdownItem>Move to pipline</BDropdownItem>
                    <BDropdownItem>Add to exceptions</BDropdownItem>
                    <BDropdownItem>Switch to common form view</BDropdownItem>
                    <BDropdownItem>Reset form view to default</BDropdownItem>
                  </BDropdown>
                </div>
              </BCol>
            </BRow>
          </BCardHeader>
          <BCardBody>
            <div>
              <div class="table-responsive table-card">
                <table class="table align-middle" id="customerTable">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" style="width: 50px">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="checkAll" value="option" />
                        </div>
                      </th>
                      <th class="sort" data-sort="name" @click="onSort('fname')">Name</th>
                      <th class="sort" data-sort="company_name" @click="onSort('companyName')">Company</th>
                      <th class="sort" data-sort="leads_score" @click="onSort('score')">Leads Source</th>
                      <th class="sort" data-sort="phone" @click="onSort('phone_mobile')">Phone</th>
                      <th class="sort" data-sort="location" @click="onSort('location')">Location</th>
                      <th class="sort" data-sort="tags" @click="onSort('tags')">Tags</th>
                      <th class="sort" data-sort="date" @click="onSort('date')">Create Date</th>
                      <th class="sort" data-sort="action">Action</th>
                    </tr>
                  </thead>
                  <tbody class="list form-check-all">
                    <tr v-for="(data, index) of resultQuery" :key="index">
                      <th scope="row">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="chk_child" value="option1" />
                        </div>
                      </th>
                      <td class="id" style="display: none">
                        <BLink href="javascript:void(0);" class="fw-medium link-primary">{{ data.id }}</BLink>
                      </td>
                      <td class="">
                        <div class="d-flex align-items-center">
                          <div class="flex-shrink-0">
                            <img :src="getImageSrc(data.leadImage)" alt="" class="user_image avatar-xs rounded-circle">
                          </div>
                          <div class="flex-grow-1 ms-2 name">{{ data.name }}</div>
                        </div>
                      </td>
                      <td class="company_name">{{ data.company }}</td>
                      <td class="leads_score">{{ data.leadSource }}</td>
                      <td class="phone">{{ data.phone }}</td>
                      <td class="location">{{ data.location }}</td>
                      <td class="tags">
                        <span v-for="(tag, index) of data.tags" :key="index">
                          &nbsp;<BBadge variant="primary-subtle" class="bg-primary-subtle text-primary"> {{ tag }}</BBadge>
                        </span>
                      </td>
                      <td class="date">{{ data.created_at }}</td>
                      <td>
                        <ul class="list-inline hstack gap-2 mb-0">
                          <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Call">
                            <BLink href="javascript:void(0);" class="text-muted d-inline-block">
                              <i class="ri-phone-line fs-16"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Message">
                            <BLink href="javascript:void(0);" class="text-muted d-inline-block">
                              <i class="ri-question-answer-line fs-16"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="View">
                            <BLink href="javascript:void(0);"><i class="ri-eye-fill align-bottom text-muted"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" @click="editDetails(data)">
                            <BLink class="edit-item-btn"><i class="ri-pencil-fill align-bottom text-muted"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" @click="deleteModalToggle(data)">
                            <BLink class="remove-item-btn">
                              <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                            </BLink>
                          </li>
                        </ul>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div class="noresult" v-if="resultQuery.length < 1">
                  <div class="text-center">
                    <lottie class="avatar-xl" colors="primary:#121331,secondary:#08a88a" :options="defaultOptions" :height="75" :width="75" />
                    <h5 class="mt-2">Sorry! No Result Found</h5>
                    <p class="text-muted mb-0">
                      We've searched more than 150+ Orders We did not find any
                      orders for you search.
                    </p>
                  </div>
                </div>
                <div class="d-flex justify-content-end p-3" v-if="resultQuery.length >= 1">
                  <div class="pagination-wrap hstack gap-2">
                    <BLink class="page-item pagination-prev" href="#" :disabled="page <= 1" @click="page--"> Previous
                    </BLink>
                    <ul class="pagination listjs-pagination mb-0">
                      <li :class="{ active: pageNumber == page, disabled: pageNumber == '...', }" v-for="(pageNumber, index) in pages" :key="index" @click="page = pageNumber">
                        <BLink class="page" href="#" data-i="1" data-page="8">{{ pageNumber }}</BLink>
                      </li>
                    </ul>
                    <BLink class="page-item pagination-next" href="#" :disabled="page >= pages.length" @click="page++">
                      Next
                    </BLink>
                  </div>
                </div>
              </div>
            </div>
            <BOffcanvas v-model="rightOffcanvas" placement="end" title="Leads Filters" header-class="bg-light" body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
              <BForm action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                  <div class="mb-4">
                    <label for="datepicker-range" class="form-label text-muted text-uppercase fw-semibold mb-3">Date</label>
                    <flat-pickr placeholder="Select date" v-model="date" :config="rangeDateconfig" class="form-control flatpickr-input" id="demo-datepicker"></flat-pickr>
                  </div>
                  <div class="mb-4">
                    <label for="country-select" class="form-label text-muted text-uppercase fw-semibold mb-3">Country</label>

                    <Multiselect class="form-control" v-model="value" :close-on-select="true" :searchable="true" :create-option="true" :options="[
                      { value: '', label: 'Select country' },
                      { value: 'Argentina', label: 'Argentina' },
                      { value: 'Belgium', label: 'Belgium' },
                      { value: 'Brazil', label: 'Brazil' },
                      { value: 'Colombia', label: 'Colombia' },
                      { value: 'Denmark', label: 'Denmark' },
                      { value: 'France', label: 'France' },
                      { value: 'Germany', label: 'Germany' },
                      { value: 'Mexico', label: 'Mexico' },
                      { value: 'Russia', label: 'Russia' },
                      { value: 'Spain', label: 'Spain' },
                      { value: 'Syria', label: 'Syria' },
                      { value: 'United Kingdom', label: 'United Kingdom' },
                      {
                        value: 'United States of America',
                        label: 'United States of America',
                      },
                    ]" />
                  </div>
                  <div class="mb-4">
                    <label for="status-select" class="form-label text-muted text-uppercase fw-semibold mb-3">Status</label>
                    <BRow class="g-2">
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1" />
                          <label class="form-check-label" for="inlineCheckbox1">New Leads</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2" />
                          <label class="form-check-label" for="inlineCheckbox2">Old Leads</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3" />
                          <label class="form-check-label" for="inlineCheckbox3">Loss Leads</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="inlineCheckbox4" value="option4" />
                          <label class="form-check-label" for="inlineCheckbox4">Follow Up</label>
                        </div>
                      </BCol>
                    </BRow>
                  </div>
                  <div class="mb-4">
                    <label for="leadscore" class="form-label text-muted text-uppercase fw-semibold mb-3">Lead
                      Score</label>
                    <BRow class="g-2 align-items-center">
                      <BCol lg>
                        <input type="number" class="form-control" id="leadscore" placeholder="0" />
                      </BCol>
                      <BCol lg="auto">To</BCol>
                      <BCol lg>
                        <input type="number" class="form-control" id="leadscore" placeholder="0" />
                      </BCol>
                    </BRow>
                  </div>
                  <div>
                    <label for="leads-tags" class="form-label text-muted text-uppercase fw-semibold mb-3">Tags</label>
                    <BRow class="g-3">
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="marketing" value="marketing" />
                          <label class="form-check-label" for="marketing">Marketing</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="management" value="management" />
                          <label class="form-check-label" for="management">Management</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="business" value="business" />
                          <label class="form-check-label" for="business">Business</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="investing" value="investing" />
                          <label class="form-check-label" for="investing">Investing</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="partner" value="partner" />
                          <label class="form-check-label" for="partner">Partner</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="lead" value="lead" />
                          <label class="form-check-label" for="lead">Leads</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="sale" value="sale" />
                          <label class="form-check-label" for="sale">Sale</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="owner" value="owner" />
                          <label class="form-check-label" for="owner">Owner</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="banking" value="banking" />
                          <label class="form-check-label" for="banking">Banking</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="banking" value="banking" />
                          <label class="form-check-label" for="banking">Exiting</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="banking" value="banking" />
                          <label class="form-check-label" for="banking">Finance</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="banking" value="banking" />
                          <label class="form-check-label" for="banking">Fashion</label>
                        </div>
                      </BCol>
                    </BRow>
                  </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                  <BButton variant="light" class="w-100">Clear Filter</BButton>
                  <BButton type="submit" variant="success" class="w-100">
                    Filters
                  </BButton>
                </div>
                <!--end offcanvas-footer-->
              </BForm>
            </BOffcanvas>
            <!--end offcanvas-->
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
    <BRow v-if=dataEdit>
      <BCol xxl="3">
        <BCard no-body>
          <BCardBody class="p-4">
            <div class="text-center">
              <div class="profile-user position-relative d-inline-block mx-auto mb-4">
                <img src="@assets/images/users/avatar-1.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image material-shadow" alt="user-profile-image" />
                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                  <input id="profile-img-file-input" type="file" class="profile-img-file-input" />
                  <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                    <span class="avatar-title rounded-circle bg-light text-body material-shadow">
                      <i class="ri-camera-fill"></i>
                    </span>
                  </label>
                </div>
              </div>
              <h5 class="fs-16 mb-1">Anna Adame</h5>
              <p class="text-muted mb-0">Lead Designer / Developer</p>
            </div>
          </BCardBody>
        </BCard>
        <BCard no-body>
          <BCardBody>
            <div class="d-flex align-items-center mb-5">
              <div class="flex-grow-1">
                <h5 class="card-title mb-0">Complete Your Profile</h5>
              </div>
              <div class="flex-shrink-0">
                <BLink href="javascript:void(0);" class="badge bg-light text-primary fs-12"><i class="ri-edit-box-line align-bottom me-1"></i> Edit</BLink>
              </div>
            </div>

            <BProgress class="animated-progress custom-progress progress-label">
              <BProgressBar :value="30" variant="danger">
                <div class="label">30%</div>
              </BProgressBar>
            </BProgress>
          </BCardBody>
        </BCard>
        <BCard no-body>
          <BCardBody>
            <div class="d-flex align-items-center mb-4">
              <div class="flex-grow-1">
                <h5 class="card-title mb-0">Portfolio</h5>
              </div>
              <div class="flex-shrink-0">
                <BLink href="javascript:void(0);" class="badge bg-light text-primary fs-12"><i class="ri-add-fill align-bottom me-1"></i> Add</BLink>
              </div>
            </div>
            <div class="mb-3 d-flex">
              <div class="avatar-xs d-block flex-shrink-0 me-3">
                <span class="avatar-title rounded-circle fs-16 bg-body text-body material-shadow">
                  <i class="ri-github-fill"></i>
                </span>
              </div>
              <input type="email" class="form-control" id="gitUsername" placeholder="Username" value="@daveadame" />
            </div>
            <div class="mb-3 d-flex">
              <div class="avatar-xs d-block flex-shrink-0 me-3">
                <span class="avatar-title rounded-circle fs-16 bg-primary material-shadow">
                  <i class="ri-global-fill"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="websiteInput" placeholder="www.example.com" value="www.velzon.com" />
            </div>
            <div class="mb-3 d-flex">
              <div class="avatar-xs d-block flex-shrink-0 me-3">
                <span class="avatar-title rounded-circle fs-16 bg-success material-shadow">
                  <i class="ri-dribbble-fill"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="dribbleName" placeholder="Username" value="@dave_adame" />
            </div>
            <div class="d-flex">
              <div class="avatar-xs d-block flex-shrink-0 me-3">
                <span class="avatar-title rounded-circle fs-16 bg-danger material-shadow">
                  <i class="ri-pinterest-fill"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="pinterestName" placeholder="Username" value="Advance Dave" />
            </div>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol xxl="9">
        <BCard no-body>
          <BCardBody class="p-4 pt-2">
            <BTabs nav-class="nav-tabs-custom rounded border-bottom-0">
              <BTab class="nav-item" title="Personal Details" active>
                <BForm action="" id="editform" class="tablelist-form" autocomplete="off">
                  <input type="hidden" id="id" />
                  <BRow class="g-3 pt-4">
                    <BCol lg="4">
                      <div>
                        <label for="jobtitle-field" class="form-label">Job Title</label>
                        <input type="text" id="jobtitle" class="form-control" placeholder="Enter Job Title" v-model="event.jobtitle" :class="{ 'is-invalid': submitted && !event.jobtitle }" />
                        <div class="invalid-feedback">Please enter job title.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="leadowner-field" class="form-label">Lead Owner</label>
                        <input type="text" disabled id="leadowner" class="form-control" placeholder="Enter Lead Owner" v-model="event.leadowner" :class="{ 'is-invalid': submitted && !event.leadowner }" />
                        <div class="invalid-feedback">Please enter lead owner.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="leadowner-field" class="form-label">Gender</label>
                        <select class="form-select mb-3" id="gender" aria-label="Default select example" v-model="event.gender" :class="{ 'is-invalid': submitted && !event.gender }" >
                          <option selected>Select gender</option>
                          <option value="Male">Male</option>
                          <option value="Female">Female</option>
                          <option value="Others">Others</option>
                        </select>
                        <div class="invalid-feedback">Please select gender.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="name-field" class="form-label">Name</label>
                        <input type="text" id="customername" class="form-control" placeholder="Enter Name" v-model="event.name" :class="{ 'is-invalid': submitted && !event.name }" />
                        <div class="invalid-feedback">Please enter a name.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="company_name-field" class="form-label">Company Name</label>
                        <input type="text" id="companyname" class="form-control" placeholder="Enter company name" v-model="event.company" :class="{ 'is-invalid': submitted && !event.company }" />
                        <div class="invalid-feedback">Please enter a company name.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="leadsource-field" class="form-label">Lead Source</label>
                        <select class="form-select mb-3" id="leadsource" aria-label="Default select example" v-model="event.leadsource" :class="{ 'is-invalid': submitted && !event.leadsource }" >
                          <option selected>Select lead source</option>
                          <option value="Male">Male</option>
                          <option value="Female">Female</option>
                          <option value="Others">Others</option>
                        </select>
                        <div class="invalid-feedback">Please select lead source.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="leadtype-field" class="form-label">Lead Type</label>
                        <select class="form-select mb-3" id="leadtype" aria-label="Default select example" v-model="event.leadtype" :class="{ 'is-invalid': submitted && !event.leadtype }" >
                          <option selected>Select lead type</option>
                          <option value="Client">Client</option>
                          <option value="Channel Partner">Channel Partner</option>
                          <option value="Consultant">Consultant</option>
                        </select>
                        <div class="invalid-feedback">Please select lead type.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="requesttype-field" class="form-label">Request Type</label>
                        <select class="form-select mb-3" id="requesttype" aria-label="Default select example" v-model="event.requesttype" :class="{ 'is-invalid': submitted && !event.requesttype }" >
                          <option selected>Select request type</option>
                          <option value="Product Enquiry">Product Enquiry</option>
                          <option value="Request for Information">Request for Information</option>
                          <option value="Suggestions">Suggestions</option>
                          <option value="Other">Other</option>
                        </select>
                        <div class="invalid-feedback">Please select request type.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="leadtype-field" class="form-label">Lead Status</label>
                        <select class="form-select mb-3" id="leadstatus" aria-label="Default select example" v-model="event.leadstatus" :class="{ 'is-invalid': submitted && !event.leadstatus }" >
                          <option selected>Select lead status</option>
                          <option value="Lead">Lead</option>
                          <option value="Open">Open</option>
                          <option value="Replied">Replied</option>
                          <option value="Opportunity">Opportunity</option>
                          <option value="Quotation">Quotation</option>
                          <option value="Lost Quotation">Lost Quotation</option>
                          <option value="Interested">Interested</option>
                          <option value="Converted">Converted</option>
                          <option value="Do Not Contact">Do Not Contact</option>
                        </select>
                        <div class="invalid-feedback">Please select lead status.</div>
                      </div>
                    </BCol>
                  </BRow>
                  <BRow>
                    <hr>
                    <h5 class="fs-15 mb-3">Contact Information</h5>
                    <BCol lg="4">
                      <div>
                        <label for="phone-field" class="form-label">Phone</label>
                        <input type="text" id="phone" class="form-control" placeholder="Enter phone no" v-model="event.phone" :class="{ 'is-invalid': submitted && !event.phone }" />
                        <div class="invalid-feedback">Please enter a phone number.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="email-field" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" placeholder="Enter email" v-model="event.email" :class="{ 'is-invalid': submitted && !event.email }" />
                        <div class="invalid-feedback">Please enter a email.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="mobile-field" class="form-label">Mobile</label>
                        <input type="text" id="mobile" class="form-control" placeholder="Enter mobile" v-model="event.mobile" :class="{ 'is-invalid': submitted && !event.mobile }" />
                        <div class="invalid-feedback">Please enter a mobile.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="website-field" class="form-label">Website</label>
                        <input type="text" id="website" class="form-control" placeholder="Enter website" v-model="event.website" :class="{ 'is-invalid': submitted && !event.website }" />
                        <div class="invalid-feedback">Please enter a website.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="whatsapp-field" class="form-label">Whats app</label>
                        <input type="text" id="whatsapp" class="form-control" placeholder="Enter whatsapp" v-model="event.whatsapp" :class="{ 'is-invalid': submitted && !event.whatsapp }" />
                        <div class="invalid-feedback">Please enter a whatsapp.</div>
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="phone-ext-field" class="form-label">Phone Extension</label>
                        <input type="text" id="phone_ext" class="form-control" placeholder="Enter phone extension" v-model="event.phone_ext" :class="{ 'is-invalid': submitted && !event.phone_ext }" />
                        <div class="invalid-feedback">Please enter a phone extension.</div>
                      </div>
                    </BCol>
                  </BRow>
                  <BRow>
                    <BCol lg="4">
                      <div>
                        <label for="location-field" class="form-label">Location</label>
                        <input type="text" id="location" class="form-control" placeholder="Enter location" v-model="event.location" :class="{ 'is-invalid': submitted && !event.location }" />
                        <div class="invalid-feedback">Please enter a location.</div>
                      </div>
                    </BCol>
                    <div>
                      <label for="" class="form-label">Tags</label>
                      <Multiselect mode="tags" :close-on-select="true" :searchable="true" :create-option="false" :options="tagoption" v-model="event.tags" :class="{ 'is-invalid': submitted && !event.tags }" />
                      <div class="invalid-feedback">Please select a tags.</div>
                    </div>
                  </BRow>
                  <BRow class="pt-4">
                    <BCol lg="6">
                      <div class="mb-3">
                        <label for="firstnameInput" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstnameInput" placeholder="Enter your firstname" value="Dave" />
                      </div>
                    </BCol>
                    <BCol lg="6">
                      <div class="mb-3">
                        <label for="lastnameInput" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastnameInput" placeholder="Enter your lastname" value="Adame" />
                      </div>
                    </BCol>
                    <BCol lg="6">
                      <div class="mb-3">
                        <label for="phonenumberInput" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phonenumberInput" placeholder="Enter your phone number" value="+(1) 987 6543" />
                      </div>
                    </BCol>
                    <BCol lg="6">
                      <div class="mb-3">
                        <label for="emailInput" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="emailInput" placeholder="Enter your email" value="daveadame@velzon.com" />
                      </div>
                    </BCol>
                    <BCol lg="12">
                      <div class="mb-3">
                        <label for="JoiningdatInput" class="form-label">Joining Date</label>

                        <flat-pickr v-model="date" id="dateinput" class="form-control"></flat-pickr>

                      </div>
                    </BCol>
                    <BCol lg="12">
                      <div class="mb-3">
                        <label for="skillsInput" class="form-label">Skills</label>
                        <Multiselect v-model="value" id="skillsinput" mode="tags" :close-on-select="false" :searchable="true" :create-option="true" :options="[
                          { value: 'illustrator', label: 'Illustrator' },
                          { value: 'photoshop', label: 'Photoshop' },
                          { value: 'css', label: 'CSS' },
                          { value: 'html', label: 'HTML' },
                          { value: 'javascript', label: 'Javascript' },
                          { value: 'python', label: 'Python' },
                          { value: 'php', label: 'PHP' },
                        ]" />
                      </div>
                    </BCol>
                    <BCol lg="6">
                      <div class="mb-3">
                        <label for="designationInput" class="form-label">Designation</label>
                        <input type="text" class="form-control" id="designationInput" placeholder="Designation" value="Lead Designer / Developer" />
                      </div>
                    </BCol>
                    <BCol lg="6">
                      <div class="mb-3">
                        <label for="websiteInput1" class="form-label">Website</label>
                        <input type="text" class="form-control" id="websiteInput1" placeholder="www.example.com" value="www.velzon.com" />
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div class="mb-3">
                        <label for="cityInput" class="form-label">City</label>
                        <input type="text" class="form-control" id="cityInput" placeholder="City" value="California" />
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div class="mb-3">
                        <label for="countryInput" class="form-label">Country</label>
                        <input type="text" class="form-control" id="countryInput" placeholder="Country" value="United States" />
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div class="mb-3">
                        <label for="zipcodeInput" class="form-label">Zip Code</label>
                        <input type="text" class="form-control" minlength="5" maxlength="6" id="zipcodeInput" placeholder="Enter zipcode" value="90011" />
                      </div>
                    </BCol>
                    <BCol lg="12">
                      <div class="mb-3 pb-2">
                        <label for="exampleFormControlTextarea" class="form-label">Description</label>
                        <textarea class="form-control" id="exampleFormControlTextarea" placeholder="Enter your description" rows="3">
                        Hi I'm Anna Adame,It will be as simple as Occidental; in fact, it will be Occidental. To an English person, it will seem like simplified English, as a skeptical Cambridge friend of mine told me what Occidental is European languages are members of the same family.</textarea>
                      </div>
                    </BCol>
                    <BCol lg="12">
                      <div class="hstack gap-2 justify-content-center">
                        <BButton type="button" variant="soft-danger" id="closemodal" @click="dataEdit = false">Close</BButton>
                        <BButton type="submit" variant="success" id="add-btn" @click="handleSubmit">
                          {{ dataEdit ? 'Update' : 'Add leads' }}
                        </BButton>
                      </div>
                    </BCol>
                  </BRow>
                </BForm>
              </BTab>
              <BTab title="Change Password">
                <form action="javascript:void(0);">
                  <BRow class="g-2 pt-4">
                    <BCol lg="4">
                      <div>
                        <label for="oldpasswordInput" class="form-label">Old Password*</label>
                        <input type="password" class="form-control" id="oldpasswordInput" placeholder="Enter current password" />
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="newpasswordInput" class="form-label">New Password*</label>
                        <input type="password" class="form-control" id="newpasswordInput" placeholder="Enter new password" />
                      </div>
                    </BCol>
                    <BCol lg="4">
                      <div>
                        <label for="confirmpasswordInput" class="form-label">Confirm Password*</label>
                        <input type="password" class="form-control" id="confirmpasswordInput" placeholder="Confirm password" />
                      </div>
                    </BCol>
                    <BCol lg="12">
                      <div class="mb-3">
                        <BLink href="javascript:void(0);" class="link-primary text-decoration-underline">Forgot
                          Password
                          ?</BLink>
                      </div>
                    </BCol>
                    <BCol lg="12">
                      <div class="text-end">
                        <BButton type="submit" variant="success">
                          Change Password
                        </BButton>
                      </div>
                    </BCol>
                  </BRow>
                </form>
                <div class="mt-4 mb-3 border-bottom pb-2">
                  <div class="float-end">
                    <BLink href="javascript:void(0);" class="link-primary">All Logout</BLink>
                  </div>
                  <h5 class="card-title">Login History</h5>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-light text-primary rounded-3 fs-18 material-shadow">
                      <i class="ri-smartphone-line"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6>iPhone 12 Pro</h6>
                    <p class="text-muted mb-0">
                      Los Angeles, United States - March 16 at 2:47PM
                    </p>
                  </div>
                  <div>
                    <BLink href="javascript:void(0);">Logout</BLink>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-light text-primary rounded-3 fs-18 material-shadow">
                      <i class="ri-tablet-line"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6>Apple iPad Pro</h6>
                    <p class="text-muted mb-0">
                      Washington, United States - November 06 at 10:43AM
                    </p>
                  </div>
                  <div>
                    <BLink href="javascript:void(0);">Logout</BLink>
                  </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-light text-primary rounded-3 fs-18 material-shadow">
                      <i class="ri-smartphone-line"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6>Galaxy S21 Ultra 5G</h6>
                    <p class="text-muted mb-0">
                      Conneticut, United States - June 12 at 3:24PM
                    </p>
                  </div>
                  <div>
                    <BLink href="javascript:void(0);">Logout</BLink>
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar-sm">
                    <div class="avatar-title bg-light text-primary rounded-3 fs-18 material-shadow">
                      <i class="ri-macbook-line"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6>Dell Inspiron 14</h6>
                    <p class="text-muted mb-0">
                      Phoenix, United States - July 26 at 8:10AM
                    </p>
                  </div>
                  <div>
                    <BLink href="javascript:void(0);">Logout</BLink>
                  </div>
                </div>
              </BTab>
              <BTab title="Experience">
                <form>
                  <div id="newlink">
                    <div id="1">
                      <BRow class="pt-4">
                        <BCol lg="12">
                          <div class="mb-3">
                            <label for="jobTitle" class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="jobTitle" placeholder="Job title" value="Lead Designer / Developer" />
                          </div>
                        </BCol>
                        <BCol lg="6">
                          <div class="mb-3">
                            <label for="companyName" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="companyName" placeholder="Company name" value="Themesbrand" />
                          </div>
                        </BCol>
                        <BCol lg="6">
                          <div class="mb-3">
                            <label for="experienceYear" class="form-label">Experience Years</label>
                            <BRow>
                              <BCol lg="5">
                                <Multiselect class="form-control" v-model="value2" :close-on-select="true" :searchable="true" :create-option="true" :options="[
                                  { value: '', label: 'Select years' },
                                  { value: 'Choice 1', label: '2001' },
                                  { value: 'Choice 2', label: '2002' },
                                  { value: 'Choice 3', label: '2003' },
                                  { value: 'Choice 4', label: '2004' },
                                  { value: 'Choice 5', label: '2005' },
                                  { value: 'Choice 6', label: '2006' },
                                  { value: 'Choice 7', label: '2007' },
                                  { value: 'Choice 8', label: '2008' },
                                  { value: 'Choice 9', label: '2009' },
                                  { value: 'Choice 10', label: '2010' },
                                  { value: 'Choice 11', label: '2011' },
                                  { value: 'Choice 12', label: '2012' },
                                  { value: 'Choice 13', label: '2013' },
                                  { value: 'Choice 14', label: '2014' },
                                  { value: 'Choice 15', label: '2015' },
                                  { value: 'Choice 16', label: '2016' },
                                  { value: 'Choice 17', label: '2017' },
                                  { value: 'Choice 18', label: '2018' },
                                  { value: 'Choice 19', label: '2019' },
                                  { value: 'Choice 20', label: '2020' },
                                  { value: 'Choice 21', label: '2021' },
                                  { value: 'Choice 22', label: '2022' }


                                ]" />
                              </BCol>
                              <BCol cols="auto" class="align-self-center">to</BCol>
                              <BCol lg="5">


                                <Multiselect class="form-control" v-model="value1" :close-on-select="true" :searchable="true" :create-option="true" :options="[
                                  { value: '', label: 'Select years' },
                                  { value: 'Choice 1', label: '2001' },
                                  { value: 'Choice 2', label: '2002' },
                                  { value: 'Choice 3', label: '2003' },
                                  { value: 'Choice 4', label: '2004' },
                                  { value: 'Choice 5', label: '2005' },
                                  { value: 'Choice 6', label: '2006' },
                                  { value: 'Choice 7', label: '2007' },
                                  { value: 'Choice 8', label: '2008' },
                                  { value: 'Choice 9', label: '2009' },
                                  { value: 'Choice 10', label: '2010' },
                                  { value: 'Choice 11', label: '2011' },
                                  { value: 'Choice 12', label: '2012' },
                                  { value: 'Choice 13', label: '2013' },
                                  { value: 'Choice 14', label: '2014' },
                                  { value: 'Choice 15', label: '2015' },
                                  { value: 'Choice 16', label: '2016' },
                                  { value: 'Choice 17', label: '2017' },
                                  { value: 'Choice 18', label: '2018' },
                                  { value: 'Choice 19', label: '2019' },
                                  { value: 'Choice 20', label: '2020' },
                                  { value: 'Choice 21', label: '2021' },
                                  { value: 'Choice 22', label: '2022' }


                                ]" />
                              </BCol>
                            </BRow>
                          </div>
                        </BCol>
                        <BCol lg="12">
                          <div class="mb-3">
                            <label for="jobDescription" class="form-label">Job Description</label>
                            <textarea class="form-control" id="jobDescription" rows="3" placeholder="Enter description">
                            You always want to make sure that your fonts work well together and try to limit the number of fonts you use to three or less. Experiment and play around with the fonts that you already have in the software you're working with reputable font websites. </textarea>
                          </div>
                        </BCol>
                        <div class="hstack gap-2 justify-content-end">
                          <BLink class="btn btn-success" href="javascript:void(0);">Delete</BLink>
                        </div>
                      </BRow>
                    </div>
                  </div>
                  <div id="newForm" style="display: none"></div>
                  <BCol lg="12">
                    <div class="hstack gap-2">
                      <BButton type="button" variant="success">
                        Update
                      </BButton>
                      <BLink href="javascript:void(0);" class="btn btn-primary">Add New</BLink>
                    </div>
                  </BCol>
                </form>
              </BTab>
              <BTab title="Privacy Policy">
                <div class="mb-4 pb-2 pt-4">
                  <h5 class="card-title text-decoration-underline mb-3">
                    Security:
                  </h5>
                  <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0">
                    <div class="flex-grow-1">
                      <h6 class="fs-14 mb-1">Two-factor Authentication</h6>
                      <p class="text-muted">
                        Two-factor authentication is an enhanced security
                        meansur. Once enabled, you'll be required to give two
                        types of identification when you log into Google
                        Authentication and SMS are Supported.
                      </p>
                    </div>
                    <div class="flex-shrink-0 ms-sm-3">
                      <BLink href="javascript:void(0);" class="btn btn-sm btn-primary">Enable Two-facor Authentication
                      </BLink>
                    </div>
                  </div>
                  <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                    <div class="flex-grow-1">
                      <h6 class="fs-14 mb-1">Secondary Verification</h6>
                      <p class="text-muted">
                        The first factor is a password and the second commonly
                        includes a text with a code sent to your smartphone, or
                        biometrics using your fingerprint, face, or retina.
                      </p>
                    </div>
                    <div class="flex-shrink-0 ms-sm-3">
                      <BLink href="javascript:void(0);" class="btn btn-sm btn-primary">Set up secondary method</BLink>
                    </div>
                  </div>
                  <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                    <div class="flex-grow-1">
                      <h6 class="fs-14 mb-1">Backup Codes</h6>
                      <p class="text-muted mb-sm-0">
                        A backup code is automatically generated for you when
                        you turn on two-factor authentication through your iOS
                        or Android Twitter app. You can also generate a backup
                        code on twitter.com.
                      </p>
                    </div>
                    <div class="flex-shrink-0 ms-sm-3">
                      <BLink href="javascript:void(0);" class="btn btn-sm btn-primary">Generate backup codes</BLink>
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <h5 class="card-title text-decoration-underline mb-3">
                    Application Notifications:
                  </h5>
                  <ul class="list-unstyled mb-0">
                    <li class="d-flex">
                      <div class="flex-grow-1">
                        <label for="directMessage" class="form-check-label fs-14">Direct messages</label>
                        <p class="text-muted">
                          Messages from people you follow
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" role="switch" id="directMessage" checked />
                        </div>
                      </div>
                    </li>
                    <li class="d-flex mt-2">
                      <div class="flex-grow-1">
                        <label class="form-check-label fs-14" for="desktopNotification">
                          Show desktop notifications
                        </label>
                        <p class="text-muted">
                          Choose the option you want as your default setting.
                          Block a site: Next to "Not allowed to send
                          notifications," click Add.
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" role="switch" id="desktopNotification" checked />
                        </div>
                      </div>
                    </li>
                    <li class="d-flex mt-2">
                      <div class="flex-grow-1">
                        <label class="form-check-label fs-14" for="emailNotification">
                          Show email notifications
                        </label>
                        <p class="text-muted">
                          Under Settings, choose Notifications. Under Select an
                          account, choose the account to enable notifications
                          for.
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" role="switch" id="emailNotification" />
                        </div>
                      </div>
                    </li>
                    <li class="d-flex mt-2">
                      <div class="flex-grow-1">
                        <label class="form-check-label fs-14" for="chatNotification">
                          Show chat notifications
                        </label>
                        <p class="text-muted">
                          To prevent duplicate mobile notifications from the
                          Gmail and Chat apps, in settings, turn off Chat
                          notifications.
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" role="switch" id="chatNotification" />
                        </div>
                      </div>
                    </li>
                    <li class="d-flex mt-2">
                      <div class="flex-grow-1">
                        <label class="form-check-label fs-14" for="purchaesNotification">
                          Show purchase notifications
                        </label>
                        <p class="text-muted">
                          Get real-time purchase alerts to protect yourself from
                          fraudulent charges.
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" role="switch" id="purchaesNotification" />
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
                <div>
                  <h5 class="card-title text-decoration-underline mb-3">
                    Delete This Account:
                  </h5>
                  <p class="text-muted">
                    Go to the Data & Privacy section of your profile Account.
                    Scroll to "Your data & privacy options." Delete your Profile
                    Account. Follow the instructions to delete your account :
                  </p>
                  <div>
                    <input type="password" class="form-control" id="passwordInput" placeholder="Enter your password" value="make@321654987" style="max-width: 265px" />
                  </div>
                  <div class="hstack gap-2 mt-3">
                    <BLink href="javascript:void(0);" class="btn btn-soft-danger">Close & Delete This Account</BLink>
                    <BLink href="javascript:void(0);" class="btn btn-light">Cancel</BLink>
                  </div>
                </div>
              </BTab>
            </BTabs>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
    <BRow v-if=dataEdit>
      <BCol lg="12">
        <BCard no-body id="editcard">
          <BCardHeader class="border-0">
            <BRow class="g-4 align-items-center">
              <BCol sm="3">
                Edit Lead
              </BCol>
            </BRow>
          </BCardHeader>
          <BForm action="" id="addform" class="tablelist-form" autocomplete="off">
            <BCardBody>
              <input type="hidden" id="id" />
              <BRow class="g-3">
                <BCol lg="12">
                  <div class="text-center">
                    <div class="position-relative d-inline-block">
                      <div class="position-absolute bottom-0 end-0">
                        <label for="lead-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Select Image" data-bs-original-title="Select Image">
                          <div class="avatar-xs cursor-pointer">
                            <div class="avatar-title bg-light border rounded-circle text-muted">
                              <i class="ri-image-fill"></i>
                            </div>
                          </div>
                        </label>
                        <input class="form-control d-none" value="" id="lead-image-input" type="file" accept="image/png, image/gif, image/jpeg">
                      </div>
                      <div class="avatar-lg p-1">
                        <div class="avatar-title bg-light rounded-circle">
                          <img :src="event.leadImage || multi_user_image" id="leadImage" class="avatar-md rounded-circle object-fit-cover">
                        </div>
                      </div>
                    </div>
                    <h5 class="fs-13 mt-3">Lead Image</h5>
                  </div>
                  <div>
                    <label for="name-field" class="form-label">Name</label>
                    <input type="text" id="customername" class="form-control" placeholder="Enter Name" v-model="event.name" :class="{ 'is-invalid': submitted && !event.name }" />
                    <div class="invalid-feedback">Please enter a name.</div>
                  </div>
                </BCol>
                <BCol lg="12">
                  <div>
                    <label for="company_name-field" class="form-label">Company Name</label>
                    <input type="text" id="companyname" class="form-control" placeholder="Enter company name" v-model="event.company" :class="{ 'is-invalid': submitted && !event.company }" />
                    <div class="invalid-feedback">Please enter a company name.</div>
                  </div>
                </BCol>
                <BCol lg="6">
                  <div>
                    <label for="leads_score-field" class="form-label">Leads Score</label>
                    <input type="text" id="leadsscore" class="form-control" placeholder="Enter lead score" v-model="event.score" :class="{ 'is-invalid': submitted && !event.score }" />
                    <div class="invalid-feedback">Please enter a lead score.</div>
                  </div>
                </BCol>
                <BCol lg="6">
                  <div>
                    <label for="phone-field" class="form-label">Phone</label>
                    <input type="text" id="phone" class="form-control" placeholder="Enter phone no" v-model="event.phone" :class="{ 'is-invalid': submitted && !event.phone }" />
                    <div class="invalid-feedback">Please enter a phone number.</div>
                  </div>
                </BCol>
                <BCol lg="6">
                  <div>
                    <label for="phone-field" class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" placeholder="Enter email" v-model="event.email" :class="{ 'is-invalid': submitted && !event.email }" />
                    <div class="invalid-feedback">Please enter a email.</div>
                  </div>
                </BCol>
                <BCol lg="6">
                  <div>
                    <label for="location-field" class="form-label">Location</label>
                    <input type="text" id="location" class="form-control" placeholder="Enter location" v-model="event.location" :class="{ 'is-invalid': submitted && !event.location }" />
                    <div class="invalid-feedback">Please enter a location.</div>
                  </div>
                </BCol>
                <div>
                  <label for="" class="form-label">Tags</label>
                  <Multiselect mode="tags" :close-on-select="true" :searchable="true" :create-option="false" :options="tagoption" v-model="event.tags" :class="{ 'is-invalid': submitted && !event.tags }" />
                  <div class="invalid-feedback">Please select a tags.</div>
                </div>
              </BRow>
            </BCardBody>
            <BCardFooter>
              <div class="hstack gap-2 justify-content-center">
                <BButton type="button" variant="light" id="closemodal" @click="dataEdit = false">Close</BButton>
                <BButton type="submit" variant="success" id="add-btn" @click="handleSubmit">
                  {{ dataEdit ? 'Update' : 'Add leads' }}
                </BButton>
              </div>
            </BCardFooter>
          </BForm>
        </BCard>
      </BCol>
    </BRow>
    <BModal v-model="addLeadsModal" size="lg" id="showmodal" hide-footer title-class="exampleModalLabel" :title="dataEdit ? 'Edit Lead' : 'Add Lead'" header-class="bg-light p-3" class="v-modal-custom" centered>
      <BForm action="" id="addform" class="tablelist-form" autocomplete="off">
        <input type="hidden" id="id" />
        <BRow class="g-3">
          <BCol lg="12">
            <div class="text-center">
              <div class="position-relative d-inline-block">
                <div class="position-absolute bottom-0 end-0">
                  <label for="lead-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Select Image" data-bs-original-title="Select Image">
                    <div class="avatar-xs cursor-pointer">
                      <div class="avatar-title bg-light border rounded-circle text-muted">
                        <i class="ri-image-fill"></i>
                      </div>
                    </div>
                  </label>
                  <input class="form-control d-none" value="" id="lead-image-input" type="file" accept="image/png, image/gif, image/jpeg">
                </div>
                <div class="avatar-lg p-1">
                  <div class="avatar-title bg-light rounded-circle">
                    <img :src="form.leadImage || multi_user_image" id="leadImage" class="avatar-md rounded-circle object-fit-cover">
                  </div>
                </div>
              </div>
              <h5 class="fs-13 mt-3">Lead Image</h5>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="lead_source-field" class="form-label">Lead Source</label>
               <select class="form-select mb-3" aria-label="Default select example" id="leadsource" v-model="form.leadsource" :class="{ 'is-invalid': submitted && !form.leadsource }" @change="handleLeadSourceChange">
                <option value="" disabled>Select Lead Source</option>
                <option 
                  v-for="source in leadSources" 
                  :key="source.id" 
                  :value="source.id"
                >
                  {{ source.name }}
                </option>
              </select>
              <div class="invalid-feedback">Please select lead source.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div v-if="event.leadsource == 'campaign'">
              <label for="campaignName" class="form-label">Campaign Name</label>
              <input v-model="form.campaign_name" type="text" id="campaignName" class="form-control" />
            </div>
          </BCol>
          <BCol lg="3">
            <label for="salutation" class="form-label">Salutation</label>
            <input v-model="form.salutation" type="text" id="salutation" class="form-control" />
          </BCol>
          <BCol lg="9">
            <label for="name-field" class="form-label">Name</label>
            <input type="text" id="customername" class="form-control" placeholder="Enter Name" v-model="event.name" :class="{ 'is-invalid': submitted && !event.name }" />
            <div class="invalid-feedback">Please enter a name.</div>
          </BCol>
          <BCol lg="6">
              <label for="series" class="form-label">Series</label>
              <input v-model="form.series" type="text" id="series" class="form-control" />
          </BCol>
          <BCol lg="6">
              <label for="jobTitle" class="form-label">Job Title</label>
              <input v-model="form.job_title" type="text" id="jobTitle" class="form-control" />
          </BCol>
          <BCol lg="6">
            <div>
              <label for="phone-field" class="form-label">Phone</label>
              <input type="text" id="phone" class="form-control" placeholder="Enter phone no" v-model="event.phone" :class="{ 'is-invalid': submitted && !event.phone }" />
              <div class="invalid-feedback">Please enter a phone number.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="phone-field" class="form-label">Email</label>
              <input type="email" id="email" class="form-control" placeholder="Enter email" v-model="event.email" :class="{ 'is-invalid': submitted && !event.email }" />
              <div class="invalid-feedback">Please enter a email.</div>
            </div>
          </BCol>
          <h5 class="mt-4">Organization</h5>
          <BCol lg="4">
            <label for="company_name-field" class="form-label">Company Name</label>
            <input type="text" id="companyname" class="form-control" placeholder="Enter company name" v-model="event.company" :class="{ 'is-invalid': submitted && !event.company }" />
            <div class="invalid-feedback">Please enter a company name.</div>
          </BCol>
          <BCol lg="4">
            <label for="annualRevenue" class="form-label">Annual Revenue</label>
            <input v-model="form.annual_revenue" type="number" id="annualRevenue" class="form-control" />
          </BCol>
          <BCol lg="4">
            <label for="industry" class="form-label">Industry</label>
            <input v-model="form.industry" type="text" id="industry" class="form-control" />
          </BCol>
          <!-- Section 4: Address -->
          <h5 class="mt-4">Address & Contacts</h5>
          <BCol lg="4">
            <label for="city" class="form-label">City</label>
            <input v-model="form.city" type="text" id="city" class="form-control" />
          </BCol>
          <BCol lg="4">
            <label for="state" class="form-label">State</label>
            <input v-model="form.state" type="text" id="state" class="form-control" />
          </BCol>
          <BCol lg="4">
            <label for="country" class="form-label">Country</label>
            <input v-model="form.country" type="text" id="country" class="form-control" />
          </BCol>
          <BCol lg="6">
              <label for="leadOwner" class="form-label">Lead Owner</label>
              <input v-model="form.lead_owner" type="email" id="leadOwner" class="form-control" />
          </BCol>
          <BCol lg="6">
            <label for="" class="form-label">Tags</label>
            <Multiselect mode="tags" :close-on-select="true" :searchable="true" :create-option="false" :options="tagoption" v-model="event.tags" :class="{ 'is-invalid': submitted && !event.tags }" />
            <div class="invalid-feedback">Please select a tags.</div>
          </BCol>
        </BRow>
        <div class="modal-footer v-modal-footer">
          <div class="hstack gap-2 justify-content-end">
            <BButton type="button" variant="light" id="closemodal" @click="addLeadsModal = false">Close</BButton>
            <BButton type="submit" variant="success" id="add-btn" @click="handleSubmit">
              {{ dataEdit ? 'Update' : 'Add leads' }}
            </BButton>
          </div>
        </div>
      </BForm>
    </BModal>

    <!-- delete modal -->
    <BModal v-model="deleteModal" modal-class="zoomIn" hide-footer no-close-on-backdrop centered>
      <div class="mt-2 text-center">
        <lottie class="avatar-xl" colors="primary:#f7b84b,secondary:#f06548" :options="defaultOptions1" :height="100" :width="100" />
        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
          <h4>Are you sure ?You are about to delete a lead ?</h4>
          <p class="text-muted mx-4 mb-0">Deleting your lead will remove all of your information from our database.</p>
        </div>
      </div>
      <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
        <button type="button" class="btn w-sm btn-light material-shadow-none" @click="deleteModal = false">Close</button>
        <button type="button" class="btn w-sm btn-danger" id="delete-record" @click="deleteData">Yes, Delete It!</button>
      </div>
    </BModal>
  </Layout>
</template>
<style> .open-activities {  min-height: 50px;  padding-left: 0px;  padding-bottom: 15px !important; }  .open-activities .new-btn {  text-align: right; }  .single-activity {  min-height: 90px;  border: 1px solid var(--border-color);  padding: 10px;  border-bottom: 0;  padding-right: 0; }  .single-activity:last-child {  border-bottom: 1px solid var(--border-color); }  .single-activity:hover .completion-checkbox{  display: block; }  .completion-checkbox {  vertical-align: middle;  display: none; }  .checkbox {  min-width: 22px; }  .open-tasks {  width: 50%; }  .open-tasks:first-child {  border-right: 0; }  .open-events {  width: 50%; }  .open-section-head {  background-color: var(--bg-color);  min-height: 30px;  border-bottom: 1px solid var(--border-color);  padding: 10px;  font-weight: bold; }  .no-activity {     text-align: center;     padding-top: 30px; }  .form-footer {  background-color: var(--bg-color); } 
 .comment-content {     border: 1px solid var(--border-color);  border-bottom: none; }  .comment-content:last-child {     border-bottom: 1px solid var(--border-color); }  .new-btn {  text-align: right; }  .notes-section .no-activity {  min-height: 100px;  text-align: center; }  .notes-section .btn {  padding: 0.2rem 0.2rem; }  </style>