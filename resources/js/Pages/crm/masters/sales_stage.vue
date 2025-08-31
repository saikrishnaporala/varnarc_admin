<script>
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
    components: {
        Layout,
        PageHeader,
        Multiselect,
        flatPickr,
        Head
    },
    data() {
        return {
            modalShow: false,
            value: null,
            date: null,
            timeConfig: {
                enableTime: false,
                dateFormat: "d M, Y",
            },
        };
    },
};
</script>

<template>
    <Layout>
        <Head title="Sales Stage" />
        <PageHeader title="Sales Stage" pageTitle="CRM" subTitle="Masters"/>
        <BCard no-body>
            <BCardBody>
                <BRow class="g-3">
                    <BCol md="3">
                        <div class="search-box">
                            <input type="text" class="form-control search" placeholder="Search for deals...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </BCol>
                    <BCol md="auto" class="ms-auto">
                        <div class="d-flex hastck gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted flex-shrink-0">Sort by: </span>

                                <Multiselect class="form-control" style="width:130px;" v-model="value"
                                    :close-on-select="true" :searchable="true" :create-option="true" :options="[
                                        { value: 'Owner', label: 'Owner' },
                                        { value: 'Company', label: 'Company' },
                                        { value: 'Date', label: 'Date' },

                                    ]" />

                            </div>
                            <BButton variant="success" @click="modalShow = !modalShow">
                                <i class="ri-add-fill align-bottom me-1"></i> Add Deals
                            </BButton>
                            <BDropdown variant="link" class="card-header-dropdown"
                                toggle-class="btn btn-soft-info btn-icon fs-14" no-caret menu-class="dropdown-menu-end">
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
            </BCardBody>
        </BCard>

        <BRow class="row-cols-xxl-5 row-cols-lg-3 row-cols-md-2 row-cols-1">
            <BCol>
                <BCard no-body>
                    <BLink class="card-header bg-danger-subtle" role="button" v-b-toggle.leadDiscovered>
                        <h5 class="card-title text-uppercase fw-semibold mb-1 fs-15">Lead Discovered</h5>
                        <p class="text-muted mb-0">$265,200 <span class="fw-medium">4 Deals</span></p>
                    </BLink>
                </BCard>
                <BCollapse id="leadDiscovered" visible>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.leadDiscovered1>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Managing sales team meeting</h6>
                                    <p class="text-muted mb-0">$87k - 01 Jan, 2022</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="leadDiscovered1">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.leadDiscovered2>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Airbnb React Development</h6>
                                    <p class="text-muted mb-0">$20.3k - 24 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="leadDiscovered2">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1 ribbon-box ribbon-fill ribbon-sm">
                        <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.leadDiscovered3>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Discovery Capital</h6>
                                    <p class="text-muted mb-0">$124.3k - 29 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed show" id="leadDiscovered3" visible>
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.leadDiscovered4>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-4.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Airbnb React Development</h6>
                                    <p class="text-muted mb-0">$33.6k - 24 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="leadDiscovered4">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                </BCollapse>
            </BCol>

            <BCol>
                <BCard no-body>
                    <BLink class="card-header bg-success-subtle" role="button" v-b-toggle.contactInitiated>
                        <h5 class="card-title text-uppercase fw-semibold mb-1 fs-15">Contact Initiated</h5>
                        <p class="text-muted mb-0">$108,700 <span class="fw-medium">5 Deals</span></p>
                    </BLink>
                </BCard>
                <BCollapse id="contactInitiated" visible>
                    <BCard no-body class="mb-1 ribbon-box ribbon-fill ribbon-sm">
                        <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.contactInitiated1>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-5.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Custom Mobile Apps</h6>
                                    <p class="text-muted mb-0">$28.7k - 13 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="contactInitiated1">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.contactInitiated2>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/brands/github.png" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Investment Deal for Zoetic Fashion</h6>
                                    <p class="text-muted mb-0">$32.8k - 10 Oct, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="contactInitiated2">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Zoetic Fashion <BBadge tag="small" variant="warning-subtle"
                                        class="bg-warning-subtle text-warning">25
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Today at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.contactInitiated3>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-6.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Modern Design</h6>
                                    <p class="text-muted mb-0">$23k - 03 Oct, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="contactInitiated3">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Micro Design <BBadge tag="small" variant="success-subtle"
                                        class="bg-success-subtle text-success">2
                                        Month</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Today at 11:58AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.contactInitiated4>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-7.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Hotel Logo Design</h6>
                                    <p class="text-muted mb-0">$10.9k - 05 Jan, 2022</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="contactInitiated4">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.contactInitiated5>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/brands/mail_chimp.png" alt=""
                                        class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Managing Sales</h6>
                                    <p class="text-muted mb-0">$13.3k - 04 Sep, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="contactInitiated5">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                </BCollapse>
            </BCol>

            <BCol>
                <BCard no-body>
                    <BLink class="card-header bg-warning-subtle" role="button" v-b-toggle.needsIdentified>
                        <h5 class="card-title text-uppercase fw-semibold mb-1 fs-15">Needs Identified</h5>
                        <p class="text-muted mb-0">$708,200 <span class="fw-medium">7 Deals</span></p>
                    </BLink>
                </BCard>
                <BCollapse id="needsIdentified" visible>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.needsIdentified1>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-9.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Art Studio Design</h6>
                                    <p class="text-muted mb-0">$147.5k - 24 Sep, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="needsIdentified1">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Alexis Clarke <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">7
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Alexis</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.needsIdentified2>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-8.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Billing Page Bug</h6>
                                    <p class="text-muted mb-0">$15.8k - 17 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="needsIdentified2">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Meta4Systems <BBadge variant="warning-subtle"
                                        class="bg-warning-subtle text-warning">35
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Mary</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.needsIdentified3>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/brands/dribbble.png" alt=""
                                        class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Food Selection Platform</h6>
                                    <p class="text-muted mb-0">$72.5k - 04 Jan, 2022</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="needsIdentified3">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Syntyce Solutions <BBadge variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">15
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1 ribbon-box ribbon-fill ribbon-sm">
                        <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.needsIdentified4>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Skote React Development</h6>
                                    <p class="text-muted mb-0">$89.8 - 21 Nov, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="needsIdentified4">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Themesbrand <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">3
                                        Month</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1 ribbon-box ribbon-fill ribbon-sm">
                        <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.needsIdentified5>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Velzon - Admin Dashboard</h6>
                                    <p class="text-muted mb-0">$126.7k - 30 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="needsIdentified5">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Themesbrand <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">3 Days</BBadge>
                                </h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Nancy</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.needsIdentified6>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/companies/img-6.png" alt=""
                                        class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Wood Elements Design</h6>
                                    <p class="text-muted mb-0">$120.2k - 24 Nov, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="needsIdentified6">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">iTest Factory <BBadge variant="warning-subtle" tag="small"
                                        class="bg-warning-subtle text-warning">42
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" v-b-toggle.needsIdentified7>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-10.jpg" alt=""
                                        class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">PayPal SEO audit</h6>
                                    <p class="text-muted mb-0">$135.7k - 23 Nov, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="needsIdentified7">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Meta4Systems <BBadge tag="small" variant="success-subtle"
                                        class="bg-success-subtle text-success">6
                                        Month</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                </BCollapse>
            </BCol>

            <BCol>
                <BCard no-body>
                    <BLink class="card-header bg-info-subtle" role="button" v-b-toggle.meetingArranged>
                        <h5 class="card-title text-uppercase fw-semibold mb-1 fs-15">Meeting Arranged</h5>
                        <p class="text-muted mb-0">$44,900 <span class="fw-medium">3 Deals</span></p>
                    </BLink>
                </BCard>
                <BCollapse id="meetingArranged" visible>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.meetingArranged1>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/companies/img-5.png" alt=""
                                        class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">SASS app workflow diagram</h6>
                                    <p class="text-muted mb-0">$17.8k - 01 Jan, 2022</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="meetingArranged1">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">10
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.meetingArranged2>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Uber new brand system</h6>
                                    <p class="text-muted mb-0">$24.5k - 22 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="meetingArranged2">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.meetingArranged3>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/companies/img-8.png" alt=""
                                        class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">TripAdvisor</h6>
                                    <p class="text-muted mb-0">$2.6k - 12 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="meetingArranged3">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                </BCollapse>
            </BCol>

            <BCol>
                <BCard no-body>
                    <BLink class="card-header bg-secondary-subtle" role="button" v-b-toggle.offerAccepted>
                        <h5 class="card-title text-uppercase fw-semibold mb-1 fs-15">Offer Accepted</h5>
                        <p class="text-muted mb-0">$819,300 <span class="fw-medium">8 Deals</span></p>
                    </BLink>
                </BCard>
                <BCollapse id="offerAccepted" visible>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.offerAccepted1>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-10.jpg" alt=""
                                        class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Coupon Website</h6>
                                    <p class="text-muted mb-0">$27.4k - 07 Jan, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="offerAccepted1">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1 ribbon-box ribbon-fill ribbon-sm">
                        <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.offerAccepted2>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/brands/slack.png" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Marketing Automation Demo</h6>
                                    <p class="text-muted mb-0">$94.8 - 19 Nov, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="offerAccepted2">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="warning-subtle"
                                        class="bg-warning-subtle text-warning">47
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.offerAccepted3>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-4.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">New Email Design Templates</h6>
                                    <p class="text-muted mb-0">$136.9k - 05 Jan, 2022</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="offerAccepted3">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.offerAccepted4>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-7.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Create New Components</h6>
                                    <p class="text-muted mb-0">$45.9k - 26 Dec, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="offerAccepted4">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="success-subtle"
                                        class="bg-success-subtle text-success">4
                                        Month</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1 ribbon-box ribbon-fill ribbon-sm">
                        <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.offerAccepted5>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/companies/img-3.png" alt=""
                                        class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">New Test Tickets</h6>
                                    <p class="text-muted mb-0">$118k - 01 Jan, 2022</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="offerAccepted5">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1 ribbon-box ribbon-fill ribbon-sm">
                        <div class="ribbon ribbon-info"><i class="ri-flashlight-fill"></i></div>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.offerAccepted6>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-6.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Recover Deleted Folder</h6>
                                    <p class="text-muted mb-0">$87.3k - 03 Jan, 2022</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="offerAccepted6">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">14
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body class="mb-1">
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.offerAccepted7>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/brands/github.png" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Github SEO audit</h6>
                                    <p class="text-muted mb-0">$241.2k - 21 Sep, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="offerAccepted7">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                    <BCard no-body>
                        <BCardBody>
                            <BLink class="d-flex align-items-center" role="button" v-b-toggle.offerAccepted8>
                                <div class="flex-shrink-0">
                                    <img src="@assets/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">Urban Modern Design</h6>
                                    <p class="text-muted mb-0">$67.8k - 09 Oct, 2021</p>
                                </div>
                            </BLink>
                        </BCardBody>
                        <BCollapse class="border-top border-top-dashed" id="offerAccepted8">
                            <BCardBody>
                                <h6 class="fs-14 mb-1">Nesta Technologies <BBadge tag="small" variant="danger-subtle"
                                        class="bg-danger-subtle text-danger">4
                                        Days</BBadge></h6>
                                <p class="text-muted">As a company grows however, you find it's not as easy to shout
                                    across</p>
                                <ul class="list-unstyled vstack gap-2 mb-0">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-question-answer-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Meeting with Thomas</h6>
                                                <small class="text-muted">Yesterday at 9:12AM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-mac-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Product Demo</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-xxs text-muted">
                                                <i class="ri-earth-line"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Marketing Team Meeting</h6>
                                                <small class="text-muted">Monday at 04:41PM</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </BCardBody>
                            <BCardFooter class="hstack gap-2">
                                <BButton variant="warning" size="sm" class="w-100"><i
                                        class="ri-phone-line align-bottom me-1"></i> Call</BButton>
                                <BButton variant="info" size="sm" class="w-100"><i
                                        class="ri-question-answer-line align-bottom me-1"></i> Message</BButton>
                            </BCardFooter>
                        </BCollapse>
                    </BCard>
                </BCollapse>
            </BCol>
        </BRow>
        <BRow>
            <Bcol lg="12">
                <div id="body">
                    <div class="content page-container" id="page-List/Sales Stage/List" data-page-route="List/Sales Stage/List" style="">
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
                                                    <h3 class="ellipsis title-text" title="Sales Stage">Sales Stage</h3>
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
                                                    <li>
                                                        <a class="grey-link dropdown-item" href="#" onclick="return false;">
                                                        <span class="menu-item-label" data-label="Delete"><span>De<span class="alt-underline">l</span>ete</span></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <button class="btn btn-primary btn-sm primary-action" data-label="Add Sales Stage">
                                                <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                    <use class="" href="#icon-add"></use>
                                                </svg>
                                                <span class="hidden-xs" data-label="Add%20Sales%20Stage"> <span><span class="alt-underline">A</span>dd Sales Stage</span> </span>
                                            </button>
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
                                                            </div>
                                                            <div class="level-left checkbox-actions">
                                                                <div class="level list-subject">
                                                                    <input class="level-item list-check-all" type="checkbox" title="Select All">
                                                                    <span class="level-item list-header-meta"></span>
                                                                </div>
                                                            </div>
                                                            <div class="level-right">
                                                                <span class="list-count"><span>8 of 8</span></span>
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
                                                                        <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Sales Stage" data-name="Negotiation/Review"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Sales Stage" data-name="Negotiation/Review" href="/app/sales-stage/Negotiation%2FReview" title="Negotiation/Review">Negotiation/Review</a></span>
                                                                    </div>
                                                                    <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                                                        <div class="tags-empty">-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="level-right text-muted ellipsis">
                                                                    <div class="level-item list-row-activity hidden-xs">
                                                                        <div class="hidden-md hidden-xs">
                                                                        </div>
                                                                        <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 14:01:02.882182" title="24-11-2024 14:01:02">2 d</span></span>
                                                                        <span class="comment-count d-flex align-items-center">
                                                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                <use class="" href="#es-line-chat-alt"></use>
                                                                            </svg>
                                                                            0
                                                                        </span>
                                                                        <span class="mx-2">·</span>
                                                                        <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                                                            <span class="like-action not-liked" data-doctype="Sales Stage" data-name="Negotiation/Review" data-liked-by="" title="">
                                                                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                    <use class="like-icon" href="#es-solid-heart"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="level-item visible-xs text-right">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-row-border"></div>
                                                        </div>
                                                        <div class="list-row-container" tabindex="1">
                                                            <div class="level list-row">
                                                                <div class="level-left ellipsis">
                                                                    <div class="list-row-col ellipsis list-subject level ">
                                                                        <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Sales Stage" data-name="Proposal/Price Quote"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Sales Stage" data-name="Proposal/Price Quote" href="/app/sales-stage/Proposal%2FPrice%20Quote" title="Proposal/Price Quote">Proposal/Price Quote</a></span>
                                                                    </div>
                                                                    <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                                                        <div class="tags-empty">-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="level-right text-muted ellipsis">
                                                                    <div class="level-item list-row-activity hidden-xs">
                                                                        <div class="hidden-md hidden-xs">
                                                                        </div>
                                                                        <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 14:01:02.880147" title="24-11-2024 14:01:02">2 d</span></span>
                                                                        <span class="comment-count d-flex align-items-center">
                                                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                <use class="" href="#es-line-chat-alt"></use>
                                                                            </svg>
                                                                            0
                                                                        </span>
                                                                        <span class="mx-2">·</span>
                                                                        <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                                                            <span class="like-action not-liked" data-doctype="Sales Stage" data-name="Proposal/Price Quote" data-liked-by="" title="">
                                                                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                    <use class="like-icon" href="#es-solid-heart"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="level-item visible-xs text-right">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-row-border"></div>
                                                        </div>
                                                        <div class="list-row-container" tabindex="1">
                                                            <div class="level list-row">
                                                                <div class="level-left ellipsis">
                                                                    <div class="list-row-col ellipsis list-subject level ">
                                                                        <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Sales Stage" data-name="Perception Analysis"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Sales Stage" data-name="Perception Analysis" href="/app/sales-stage/Perception%20Analysis" title="Perception Analysis">Perception Analysis</a></span>
                                                                    </div>
                                                                    <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                                                        <div class="tags-empty">-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="level-right text-muted ellipsis">
                                                                    <div class="level-item list-row-activity hidden-xs">
                                                                        <div class="hidden-md hidden-xs">
                                                                        </div>
                                                                        <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 14:01:02.878230" title="24-11-2024 14:01:02">2 d</span></span>
                                                                        <span class="comment-count d-flex align-items-center">
                                                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                <use class="" href="#es-line-chat-alt"></use>
                                                                            </svg>
                                                                            0
                                                                        </span>
                                                                        <span class="mx-2">·</span>
                                                                        <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                                                            <span class="like-action not-liked" data-doctype="Sales Stage" data-name="Perception Analysis" data-liked-by="" title="">
                                                                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                    <use class="like-icon" href="#es-solid-heart"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="level-item visible-xs text-right">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-row-border"></div>
                                                        </div>
                                                        <div class="list-row-container" tabindex="1">
                                                            <div class="level list-row">
                                                                <div class="level-left ellipsis">
                                                                    <div class="list-row-col ellipsis list-subject level ">
                                                                        <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Sales Stage" data-name="Identifying Decision Makers"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Sales Stage" data-name="Identifying Decision Makers" href="/app/sales-stage/Identifying%20Decision%20Makers" title="Identifying Decision Makers">Identifying Decision Makers</a></span>
                                                                    </div>
                                                                    <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                                                        <div class="tags-empty">-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="level-right text-muted ellipsis">
                                                                    <div class="level-item list-row-activity hidden-xs">
                                                                        <div class="hidden-md hidden-xs">
                                                                        </div>
                                                                        <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 14:01:02.876374" title="24-11-2024 14:01:02">2 d</span></span>
                                                                        <span class="comment-count d-flex align-items-center">
                                                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                <use class="" href="#es-line-chat-alt"></use>
                                                                            </svg>
                                                                            0
                                                                        </span>
                                                                        <span class="mx-2">·</span>
                                                                        <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                                                            <span class="like-action not-liked" data-doctype="Sales Stage" data-name="Identifying Decision Makers" data-liked-by="" title="">
                                                                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                    <use class="like-icon" href="#es-solid-heart"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="level-item visible-xs text-right">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-row-border"></div>
                                                        </div>
                                                        <div class="list-row-container" tabindex="1">
                                                            <div class="level list-row">
                                                                <div class="level-left ellipsis">
                                                                    <div class="list-row-col ellipsis list-subject level ">
                                                                        <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Sales Stage" data-name="Value Proposition"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Sales Stage" data-name="Value Proposition" href="/app/sales-stage/Value%20Proposition" title="Value Proposition">Value Proposition</a></span>
                                                                    </div>
                                                                    <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                                                        <div class="tags-empty">-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="level-right text-muted ellipsis">
                                                                    <div class="level-item list-row-activity hidden-xs">
                                                                        <div class="hidden-md hidden-xs">
                                                                        </div>
                                                                        <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 14:01:02.873666" title="24-11-2024 14:01:02">2 d</span></span>
                                                                        <span class="comment-count d-flex align-items-center">
                                                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                <use class="" href="#es-line-chat-alt"></use>
                                                                            </svg>
                                                                            0
                                                                        </span>
                                                                        <span class="mx-2">·</span>
                                                                        <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                                                            <span class="like-action not-liked" data-doctype="Sales Stage" data-name="Value Proposition" data-liked-by="" title="">
                                                                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                    <use class="like-icon" href="#es-solid-heart"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="level-item visible-xs text-right">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-row-border"></div>
                                                        </div>
                                                        <div class="list-row-container" tabindex="1">
                                                            <div class="level list-row">
                                                                <div class="level-left ellipsis">
                                                                    <div class="list-row-col ellipsis list-subject level ">
                                                                        <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Sales Stage" data-name="Needs Analysis"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Sales Stage" data-name="Needs Analysis" href="/app/sales-stage/Needs%20Analysis" title="Needs Analysis">Needs Analysis</a></span>
                                                                    </div>
                                                                    <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                                                        <div class="tags-empty">-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="level-right text-muted ellipsis">
                                                                    <div class="level-item list-row-activity hidden-xs">
                                                                        <div class="hidden-md hidden-xs">
                                                                        </div>
                                                                        <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 14:01:02.870963" title="24-11-2024 14:01:02">2 d</span></span>
                                                                        <span class="comment-count d-flex align-items-center">
                                                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                <use class="" href="#es-line-chat-alt"></use>
                                                                            </svg>
                                                                            0
                                                                        </span>
                                                                        <span class="mx-2">·</span>
                                                                        <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                                                            <span class="like-action not-liked" data-doctype="Sales Stage" data-name="Needs Analysis" data-liked-by="" title="">
                                                                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                    <use class="like-icon" href="#es-solid-heart"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="level-item visible-xs text-right">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-row-border"></div>
                                                        </div>
                                                        <div class="list-row-container" tabindex="1">
                                                            <div class="level list-row">
                                                                <div class="level-left ellipsis">
                                                                    <div class="list-row-col ellipsis list-subject level ">
                                                                        <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Sales Stage" data-name="Qualification"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Sales Stage" data-name="Qualification" href="/app/sales-stage/Qualification" title="Qualification">Qualification</a></span>
                                                                    </div>
                                                                    <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                                                        <div class="tags-empty">-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="level-right text-muted ellipsis">
                                                                    <div class="level-item list-row-activity hidden-xs">
                                                                        <div class="hidden-md hidden-xs">
                                                                        </div>
                                                                        <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 14:01:02.868518" title="24-11-2024 14:01:02">2 d</span></span>
                                                                        <span class="comment-count d-flex align-items-center">
                                                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                <use class="" href="#es-line-chat-alt"></use>
                                                                            </svg>
                                                                            0
                                                                        </span>
                                                                        <span class="mx-2">·</span>
                                                                        <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                                                            <span class="like-action not-liked" data-doctype="Sales Stage" data-name="Qualification" data-liked-by="" title="">
                                                                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                    <use class="like-icon" href="#es-solid-heart"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="level-item visible-xs text-right">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="list-row-border"></div>
                                                        </div>
                                                        <div class="list-row-container" tabindex="1">
                                                            <div class="level list-row">
                                                                <div class="level-left ellipsis">
                                                                    <div class="list-row-col ellipsis list-subject level ">
                                                                        <span class="level-item select-like"><input class="list-row-checkbox" type="checkbox" data-doctype="Sales Stage" data-name="Prospecting"></span><span class="level-item bold ellipsis"><a class="ellipsis" data-doctype="Sales Stage" data-name="Prospecting" href="/app/sales-stage/Prospecting" title="Prospecting">Prospecting</a></span>
                                                                    </div>
                                                                    <div class="list-row-col tag-col hide hidden-xs ellipsis">
                                                                        <div class="tags-empty">-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="level-right text-muted ellipsis">
                                                                    <div class="level-item list-row-activity hidden-xs">
                                                                        <div class="hidden-md hidden-xs">
                                                                        </div>
                                                                        <span class="modified"><span class="frappe-timestamp  mini" data-timestamp="2024-11-24 14:01:02.859328" title="24-11-2024 14:01:02">2 d</span></span>
                                                                        <span class="comment-count d-flex align-items-center">
                                                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                <use class="" href="#es-line-chat-alt"></use>
                                                                            </svg>
                                                                            0
                                                                        </span>
                                                                        <span class="mx-2">·</span>
                                                                        <span class="list-row-like hidden-xs" style="margin-bottom: 1px;">
                                                                            <span class="like-action not-liked" data-doctype="Sales Stage" data-name="Prospecting" data-liked-by="" title="">
                                                                                <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                                                                    <use class="like-icon" href="#es-solid-heart"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </span>
                                                                    </div>
                                                                    <div class="level-item visible-xs text-right">
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
                                                                <p>You haven't created a Sales Stage yet</p>
                                                                <p><button class="btn btn-default btn-sm btn-new-doc hidden-xs">
                                                                    Create your first Sales Stage
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
                    </div>
                </div>
            </Bcol>
        </BRow>
        <!-- <b-modal v-model="modalShow" body-class="p-0" header-class="bg-light p-3" hide-footer title="Create Deals"
                class="v-modal-custom" centered>
                <form>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="dealTitle" class="form-label">Deal Title</label>
                            <input type="email" class="form-control" id="dealTitle" placeholder="Enter title">
                        </div>
                        <div class="mb-3">
                            <label for="dealValue" class="form-label">Value (USD)</label>
                            <input type="text" class="form-control" id="dealValue" placeholder="Enter value">
                        </div>
                        <div class="mb-3">
                            <label for="dealOwner" class="form-label">Deals Owner</label>
                            <input type="text" class="form-control" id="dealOwner" placeholder="Enter owner name">
                        </div>
                        <div class="mb-3">
                            <label for="dueDate" class="form-label">Due Date</label>
                            <flat-pickr placeholder="Select date" v-model="date" :config="timeConfig"
                                class="form-control flatpickr-input" id="adddate"></flat-pickr>
                        </div>
                        <div class="mb-3">
                            <label for="contactNumber" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="contactNumber" placeholder="Enter contact number">
                        </div>
                        <div class="mb-3">
                            <label for="contactNumber" class="form-label">Description</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"
                                placeholder="Enter description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <BButton type="button" variant="light" @click="modalShow = false">Close
                        </BButton>
                        <BButton type="button" variant="success"><i class="ri-save-line align-bottom me-1"></i> Save
                        </BButton>
                    </div>
                </b-form>
            </b-modal> -->

        <BModal v-model="modalShow" body-class="" header-class="bg-light p-3" hide-footer title="Create Deals"
            class="v-modal-custom" centered>
            <BForm class="needs-validation" novalidate id="deals-form" onsubmit="return false">
                <div class="mb-3">
                    <label for="deatType" class="form-label">Deals Type</label>
                    <select class="form-select" id="deatType" data-choices aria-label="Default select example" required>
                        <option value="" data-custom-properties="[object Object]">Select deals type</option>
                        <option value="Lead Disovered">Lead Disovered</option>
                        <option value="Contact Initiated">Contact Initiated</option>
                        <option value="Need Identified">Need Identified</option>
                        <option value="Meeting Arranged">Meeting Arranged</option>
                        <option value="Offer Accepted">Offer Accepted</option>
                    </select>
                    <div class="invalid-feedback">
                        Please write an deals owner name.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="dealTitle" class="form-label">Deal Title</label>
                    <input type="text" class="form-control" id="dealTitle" placeholder="Enter title" required>
                    <div class="invalid-feedback">
                        Please write a title.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="dealValue" class="form-label">Value (USD)</label>
                    <input type="number" class="form-control" id="dealValue" step="0.01" placeholder="Enter value" required>
                    <div class="invalid-feedback">
                        Please write a value.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="dealOwner" class="form-label">Deals Owner</label>
                    <input type="text" class="form-control" id="dealOwner" required placeholder="Enter owner name">
                    <div class="invalid-feedback">
                        Please write an deals owner name.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="dueDate" class="form-label">Due Date</label>
                    <flat-pickr placeholder="Select date" v-model="date" :config="timeConfig"
                        class="form-control flatpickr-input" id="adddate"></flat-pickr>
                    <div class="invalid-feedback">
                        Please select a due date.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="dealEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="dealEmail" placeholder="Enter email" required>
                    <div class="invalid-feedback">
                        Please write a email.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="contactNumber" class="form-label">Contact</label>
                    <input type="text" class="form-control" id="contactNumber" placeholder="Enter contact number" required>
                    <div class="invalid-feedback">
                        Please add a contact.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="contactDescription" class="form-label">Description</label>
                    <textarea class="form-control" id="contactDescription" rows="3" placeholder="Enter description"
                        required></textarea>
                    <div class="invalid-feedback">
                        Please add a description.
                    </div>
                </div>
                <div class="modal-footer v-modal-footer">
                    <BButton type="button" variant="light" id="close-modal" @click="modalShow = false"> Close </BButton>
                    <BButton type="submit" variant="success"><i class="ri-save-line align-bottom me-1"></i> Save </BButton>
                </div>
            </BForm>
        </BModal>
    </Layout>
</template>