<script lang="ts" setup>
import { ref, computed, onMounted, watch } from 'vue';
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


interface Form {
  id?: number;
  name: string;
  details: string | null;
}

interface ApiResponse<T> {
  message: string;
  lead_source?: T;
}

const submitted = ref(false);
const dataEdit = ref(false);
const deleteModal = ref(false);
const leadSources = ref<Form[]>([]);
let editId: number | null = null;
const event = ref<Form>({
  name: '',
  details: null,
});

// Reactive variables
const rightOffcanvas = ref(false);
const formModal = ref(false);
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


// Computed properties
const displayedPosts = computed(() => paginate(leadSources.value));
const resultQuery = computed(() => {
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase();
    return displayedPosts.value.filter((data: { id: string; name: string; details: string; }) => {
      return (
        data.id.toLowerCase().includes(search) ||
        data.name.toLowerCase().includes(search) ||
        data.details.toLowerCase().includes(search)
      );
    });
  } else {
    return displayedPosts.value;
  }
});

// Methods
const handleSubmit = async () => {
  submitted.value = true;
  if (event.value.name && event.value.details) {
    const url = dataEdit.value ? `/api/lead-sources/${editId}` : '/api/lead-sources';
    console.log(url);
    if (dataEdit.value) {
      leadSources.value = leadSources.value.map((item) =>
        item.id === event.value.id
          ? { ...item, ...event.value }
          : item
      );
      try {
        // POST request to your API endpoint
        const response = await axios.put(url, event.value);

        // Handle the response if needed
        console.log('Lead updated successfully:', response.data);
      } catch (error) {
        // Handle the error
        console.error('Error submitting lead:', error);
      }
    } else {
      const newLead = {
        id: Math.floor(Math.random() * 100 + 20) - 20,
        ...event.value
      };
      try {
        // POST request to your API endpoint
        const response = await axios.post(url, newLead);

        // Handle the response if needed
        console.log('Lead submitted successfully:', response.data);
      } catch (error) {
        // Handle the error
        console.error('Error submitting lead:', error);
      }
      leadSources.value = [newLead, ...leadSources.value];
    }
    formModal.value = false;
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

const editDetails = (data: any) => {
  formModal.value = true;
  dataEdit.value = true;
  editId = data.id;
  console.log(data);
  
  event.value = {
    id: data.id,
    details: data.details,
    name: data.name,
  };
  submitted.value = false;
};

const toggleModal = () => {
  formModal.value = true;
  dataEdit.value = false;
  event.value = {
    id: 0,
    details: "",
    name: "",
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
  const numberOfPages = Math.ceil(leadSources.value.length / perPage.value);
  pages.value = Array.from({ length: numberOfPages }, (_, i) => i + 1);
};

const paginate = (items: typeof leadSources.value) => {
  const from = (page.value - 1) * perPage.value;
  return items.slice(from, from + perPage.value);
};

onMounted(() => {
  fetchLeadSource();
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

</script>

<style>
#mobileAuthNavBar {
  min-height: 125px;
  justify-content: space-between;
}
</style>

<template>
  <Layout>
    <Head title="Lead Source" />
    <PageHeader title="Lead Source" pageTitle="CRM" subTitle="Masters"/>
    <BRow>
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
                    <i class="ri-add-line align-bottom me-1"></i> Add Lead Source
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
                <table class="table align-middle table-nowrap mb-0" id="customerTable">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" style="width: 50px">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="checkAll" value="option" />
                        </div>
                      </th>
                      <th class="sort" data-sort="name" scope="col" @click="onSort('name')">Lead Source</th>
                      <th class="sort" data-sort="owner" scope="col" @click="onSort('parent')">Details</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody class="list form-check-all">
                    <tr v-for="(data, index) of leadSources" :key="index">
                      <th scope="row">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="chk_child" value="option1" />
                        </div>
                      </th>
                      <td class="id" style="display: none">
                        <BLink href="javascript:void(0);" class="fw-medium link-primary">{{ data.id }}</BLink>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="flex-grow-1 ms-2 name">
                            {{ data.name }}
                          </div>
                        </div>
                      </td>
                      <td class="details">{{ data.details }}</td>
                      <td>
                        <ul class="list-inline hstack gap-2 mb-0">
                          <li class="list-inline-item" data-bs-trigger="hover" title="Edit" @click="editDetails(data)">
                            <BButton variant="primary" class="waves-effect waves-light edit-item-btn"><i class="ri-pencil-fill"></i></BButton>
                          </li>
                          <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Delete" @click="deleteModalToggle(data)">
                            <BButton variant="danger" class="waves-effect waves-light"><i class="ri-delete-bin-fill"></i></BButton>
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
    <BModal v-model="formModal" id="showmodal" hide-footer title-class="exampleModalLabel" :title="dataEdit ? 'Edit Lead Source' : 'Add Lead Source'" header-class="bg-light p-3" class="v-modal-custom" centered>
      <BForm action="" id="addform" class="tablelist-form" autocomplete="off">
        <input type="hidden" id="id" />
        <BRow class="g-3">
          <BCol lg="12">
            <div>
              <label for="name-field" class="form-label">Lead Source</label>
              <input type="text" id="leadSource" class="form-control" placeholder="Enter Name" v-model="event.name" :class="{ 'is-invalid': submitted && !event.name }" />
              <div class="invalid-feedback">Please enter a name.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="details" class="form-label">Details</label>
              <textarea class="form-control" id="details" v-model="event.details" rows="3" placeholder="Enter details" ></textarea>
              <div class="invalid-feedback">Please enter details.</div>
            </div>
          </BCol>
        </BRow>
        <div class="modal-footer v-modal-footer">
          <div class="hstack gap-2 justify-content-end">
            <BButton type="button" variant="light" id="closemodal" @click="formModal = false">Close</BButton>
            <BButton type="submit" variant="success" id="add-btn" @click="handleSubmit">
              {{ dataEdit ? 'Update' : 'Add' }}
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