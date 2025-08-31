<script>
import { Link, Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Swal from "sweetalert2";
import animationData from "@/Components/widgets/msoeawqm.json";
import axios from 'axios';
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import multi_user_image from "@assets/images/users/multi-user.jpg";
export default {
  components: {
    Layout,
    PageHeader,
    Link,
    Head
  },
  data() {
    return {
      addCompanyModal: false,
      value: null,
      page: 1,
      perPage: 8,
      pages: [],
      searchQuery: null,
      defaultOptions: {
        animationData: animationData
      },
      defaultOptions1: {
        animationData: animationData1
      },
      multi_user_image,
      //
      submitted: false,

      dataEdit: false,
      deleteModal: false,
      event: {
        id: "",
        image_src: "",
        name: "",
        owner: "",
        industry_type: "",
        star_value: "",
        location: "",
        employee: "",
        website: "",
        contact_email: "",
        since: "",
      },
      //

    };
  },
  methods: {
    // 
    handleSubmit() {
      if (this.dataEdit) {
        this.submitted = true;
        if (this.submitted && (this.event.name && this.event.owner && this.event.industry_type && this.event.star_value && this.event.location && this.event.employee && this.event.website && this.event.contact_email && this.event.since)) {
          this.addCompanyModal = false;
          this.companies = this.companies.map(item => item.id.toString() == this.event.id.toString() ? { ...item, ...this.event } : item);
        }
      } else {
        this.submitted = true;
        if (this.submitted && (this.event.name && this.event.owner && this.event.industry_type && this.event.star_value && this.event.location && this.event.employee && this.event.website && this.event.contact_email && this.event.since)) {
          const data = {
            id: (Math.floor(Math.random() * 100 + 20) - 20),
            image_src: multi_user_image,
            ...this.event
          };
          this.companies = [data, ...this.companies];
          this.addCompanyModal = false;
        }
      }
    },

    onSort(column) {
      this.direction = this.direction === 'asc' ? 'desc' : 'asc';
      const sortedArray = [...this.companies];
      sortedArray.sort((a, b) => {
        const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
        return this.direction === 'asc' ? res : -res;
      });
      this.companies = sortedArray;
    },


    editDetails(data) {
      this.dataEdit = true;
      this.addCompanyModal = true;
      this.event = { ...data };

      this.submitted = false;
    },

    toggleModal() {
      this.addCompanyModal = true;
      this.dataEdit = false;
      this.event = {};

      this.submitted = false;
    },

    deleteModalToggle(data) {
      this.deleteModal = true;
      this.event.id = data.id;
    },

    deleteData() {
      if (this.event.id) {
        this.companies = this.companies.filter((item) => item.id != this.event.id);
        this.deleteModal = false;
      }
    },
    //

    deleteMultiple() {
      let ids_array = [];
      var items = document.getElementsByName("chk_child");
      items.forEach(function (ele) {
        if (ele.checked == true) {
          var trNode = ele.parentNode.parentNode.parentNode;
          var id = trNode.querySelector(".id a").innerHTML;
          ids_array.push(id);
        }
      });
      if (typeof ids_array !== "undefined" && ids_array.length > 0) {
        Swal.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          cancelButtonColor: "#f46a6a",
          confirmButtonColor: "#34c38f",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.value) {
            var cusList = this.companies;
            ids_array.forEach(function (id) {
              cusList = cusList.filter(function (orders) {
                return orders.id != id;
              });
            });
            this.companies = cusList;
            document.getElementById("checkAll").checked = false;
            var itemss = document.getElementsByName("chk_child");
            itemss.forEach(function (ele) {
              if (ele.checked == true) {
                ele.checked = false;
                ele.closest("tr").classList.remove("table-active");
              }
            });
          }
        });
      } else {
        Swal.fire({
          title: "Please select at least one checkbox",
          confirmButtonClass: "btn btn-info",
          buttonsStyling: true,
          showCloseButton: true,
        });
      }
    },

    showdetail(data) {
      document.getElementById('imageid').setAttribute('src', data.image_src);
      document.getElementById('cname').innerHTML = data.name;
      document.getElementById('oname').innerHTML = data.owner;
      document.getElementById('iname').innerHTML = data.industry_type;
      document.getElementById('rtng').innerHTML = data.star_value;
      document.getElementById('loc').innerHTML = data.location;
      document.getElementById('emp').innerHTML = data.employee;
      document.getElementById('webs').innerHTML = data.website;
      document.getElementById('eml').innerHTML = data.employee;
      document.getElementById('sic').innerHTML = data.since;
    },

    setPages() {
      let numberOfPages = Math.ceil(this.companies.length / this.perPage);
      this.pages = [];
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },
    paginate(companies) {
      let page = this.page;
      let perPage = this.perPage;
      let from = page * perPage - perPage;
      let to = page * perPage;
      return companies.slice(from, to);
    },

  },
  mounted() {
    var checkAll = document.getElementById("checkAll");
    if (checkAll) {
      checkAll.onclick = function () {
        var checkboxes = document.querySelectorAll('.form-check-all input[type="checkbox"]');

        if (checkAll.checked == true) {
          checkboxes.forEach(function (checkbox) {
            checkbox.checked = true;
            checkbox.closest("tr").classList.add("table-active");
            document.getElementById('remove-actions').style.display = 'block';
          });
        } else {
          checkboxes.forEach(function (checkbox) {
            checkbox.checked = false;
            checkbox.closest("tr").classList.remove("table-active");
            document.getElementById('remove-actions').style.display = 'none';
          });
        }
      };
    }

    var checkboxes = document.querySelectorAll('#companyList .form-check-input');
    Array.from(checkboxes).forEach(function (element) {
      element.addEventListener('change', function (event) {
        var checkedCount = document.querySelectorAll('#companyList .form-check-input:checked').length;

        if (event.target.closest("tr").classList.contains("table-active")) {
          (checkedCount > 0) ? document.getElementById("remove-actions").style.display = 'block' : document.getElementById("remove-actions").style.display = 'none';
        } else {
          (checkedCount > 0) ? document.getElementById("remove-actions").style.display = 'block' : document.getElementById("remove-actions").style.display = 'none';
        }
      });
    });
  },
};
</script>

<template>
  <Layout>
    <Head title="Settings" />
    <PageHeader title="Settings" pageTitle="Dashboards" />
    <BRow>
      <BCol xxl="4" md="6">
        <BCard no-body id="companyList">
          <BCardBody>
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
                          <img :src="event.image_src || multi_user_image" id="companylogo-img" class="avatar-md rounded-circle object-fit-cover">
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
                <BCol lg="6">
                  <div>
                    <label for="ownername" class="form-label">Owner Name</label>
                    <input type="text" id="ownername" class="form-control" placeholder="Enter owner name" v-model="event.owner" :class="{ 'is-invalid': submitted && !event.owner }">
                    <div class="invalid-feedback">Please enter a owner name.</div>
                  </div>
                </BCol>
                <BCol lg="6">
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
                <BCol lg="6">
                  <div>
                    <label for="website" class="form-label">Website</label>
                    <input type="text" id="website" class="form-control" placeholder="Enter website" v-model="event.website" :class="{ 'is-invalid': submitted && !event.website }">
                    <div class="invalid-feedback">Please enter a website.</div>
                  </div>
                </BCol>
                <BCol lg="6">
                  <div>
                    <label for="contactemail" class="form-label">Contact Email</label>
                    <input type="text" id="contactemail" class="form-control" placeholder="Enter contact email" v-model="event.contact_email" :class="{ 'is-invalid': submitted && !event.contact_email }">
                    <div class="invalid-feedback">Please enter a email.</div>
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
          </BCardBody>
        </BCard>
        
      </BCol>
      <BCol xxl="3" md="6">
        
      </BCol>

      <BCol xxl="3" md="6">
        
      </BCol>
      <BCol xxl="6">
        
      </BCol>
    </BRow>
  </Layout>
</template>
