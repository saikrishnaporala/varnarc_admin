<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class VelzonRoutesController extends Controller
{
    // System Settings
    public function settings() { return Inertia::render('system/settings'); }
    public function domain_settings() { return Inertia::render('system/domain-settings'); }
    public function global_default() { return Inertia::render('system/global-default'); }

    // Module Settings
    public function accounts_settings() { return Inertia::render('system/module/accounts-settings'); }
    public function stock_settings() { return Inertia::render('system/module/stock-settings'); }
    public function selling_settings() { return Inertia::render('system/module/selling-settings'); }
    public function buying_settings() { return Inertia::render('system/module/buying-settings'); }
    public function manufacturing_settings() { return Inertia::render('system/module/manufacturing-settings'); }
    public function crm_settings() { return Inertia::render('system/module/crm-settings'); }
    public function projects_settings() { return Inertia::render('system/module/projects-settings'); }
    public function support_settings() { return Inertia::render('system/module/support-settings'); }

    // Email Settings
    public function email_account() { return Inertia::render('system/email/email-account'); }
    public function email_domain() { return Inertia::render('system/email/email-domain'); }
    public function email_notification() { return Inertia::render('system/email/email-notification'); }
    public function email_template() { return Inertia::render('system/email/email-template'); }
    public function email_auto_report() { return Inertia::render('system/email/email-auto-report'); }
    public function email_news_letter() { return Inertia::render('system/email/email-news-letter'); }
    public function email_notification_settings() { return Inertia::render('system/email/email-notification-settings'); }

    // Website Settings
    public function website_settings() { return Inertia::render('system/website/website-settings'); }
    public function website_theme() { return Inertia::render('system/website/website-theme'); }
    public function website_scripts() { return Inertia::render('system/website/website-scripts'); }
    public function website_about_us_settings() { return Inertia::render('system/website/website-about-us-settings'); }
    public function website_contact_us_settings() { return Inertia::render('system/website/website-contact-us-settings'); }

    // CRM Sales
    public function crm_home() { return Inertia::render('crm/sales/home'); }
    public function crm_leads() { return Inertia::render('crm/sales/leads');}
    public function crm_opportunities() { return Inertia::render('crm/sales/opportunities');}
    public function crm_contracts() { return Inertia::render('crm/sales/contracts');}
    public function crm_appointment() { return Inertia::render('crm/sales/appointment');}
    public function crm_newsletter() { return Inertia::render('crm/sales/newsletter');}
    public function crm_companies() { return Inertia::render('crm/sales/companies'); }
    public function crm_deals() { return Inertia::render('crm/sales/deals'); }
    public function crm_communication() { return Inertia::render('crm/sales/communication'); }
    
    
    // CRM masters
    public function crm_territory() { return Inertia::render('crm/masters/territory'); }
    public function crm_customergroup() { return Inertia::render('crm/masters/customergroup');}
    public function crm_contacts() { return Inertia::render('crm/masters/contacts');}
    public function crm_prospect() { return Inertia::render('crm/masters/prospect');}
    public function crm_sales_person() { return Inertia::render('crm/masters/sales_person');}
    public function crm_sales_stage() { return Inertia::render('crm/masters/sales_stage');}
    public function crm_lead_source() { return Inertia::render('crm/masters/lead_source'); }
    
    // CRM campaigns
    public function crm_campaign() { return Inertia::render('crm/campaign/campaign'); }
    public function crm_email_campaign() { return Inertia::render('crm/campaign/email-campaign');}
    public function sms_center() { return Inertia::render('crm/campaign/sms-center');}
    public function sms_logs() { return Inertia::render('crm/campaign/sms-logs');}
    public function crm_email_groups() { return Inertia::render('crm/campaign/email-group');}

    // CRM settings
    public function crm_settings1() { return Inertia::render('crm/settings/crm-settings'); }
    public function crm_sms_settings() { return Inertia::render('crm/settings/sms-settings');}

    // CRM Maintenance
    public function crm_maintenance_schedule() { return Inertia::render('crm/maintenance/maintenance-schedule'); }
    public function crm_maintenance_visit() { return Inertia::render('crm/maintenance/maintenance-visit');}
    public function crm_warrany_claims() { return Inertia::render('crm/maintenance/warranty-claims');}

    public function dashboard()
    {
        return Inertia::render('dashboards/ecommerce/index');
    }

    public function dashboard_settings()
    {
        return Inertia::render('dashboards/settings/index');
    }

    public function dashboard_analytics()
    {
        return Inertia::render('dashboards/analytics/index');
    }

    public function dashboard_crm()
    {
        return Inertia::render('dashboards/crm/index');
    }

    public function dashboard_crypto()
    {
        return Inertia::render('dashboards/crypto/index');
    }
    public function dashboard_job()
    {
        return Inertia::render('dashboards/job/index');
    }
    public function dashboard_nft()
    {
        return Inertia::render('dashboards/nft/index');
    }
    public function dashboard_projects()
    {
        return Inertia::render('dashboards/projects/index');
    }

    public function calendar() {
        return Inertia::render('calendar/index');
    }

    public function month_grid() {
        return Inertia::render('calendar/month');
    }


    public function chat() {
        return Inertia::render('apps/chat');
    }

    public function apps_file_manager() {
        return Inertia::render('apps/filemanager');
    }

    public function apps_todo() {
        return Inertia::render('apps/todo');
    }

    public function mailbox() {
        return Inertia::render('apps/email/mailbox');
    }

    public function email_basic() {
        return Inertia::render('apps/email/email-basic');
    }

    public function email_ecommerce() {
        return Inertia::render('apps/email/email-ecommerce');
    }

    public function nft_auction() {
        return Inertia::render('apps/nft/nft-auction');
    }

    public function nft_collection() {
        return Inertia::render('apps/nft/nft-collection');
    }

    public function nft_create() {
        return Inertia::render('apps/nft/nft-create');
    }

    public function nft_creators() {
        return Inertia::render('apps/nft/nft-creators');
    }

    public function nft_explore() {
        return Inertia::render('apps/nft/nft-explore');
    }

    public function nft_item() {
        return Inertia::render('apps/nft/nft-item-detail');
    }

    public function nft_marketplace() {
        return Inertia::render('apps/nft/nft-marketplace');
    }

    public function nft_ranking() {
        return Inertia::render('apps/nft/nft-ranking');
    }

    public function nft_wallet() {
        return Inertia::render('apps/nft/nft-wallet');
    }

    public function projects_list() {
        return Inertia::render('apps/projects/list');
    }

    public function projects_overview() {
        return Inertia::render('apps/projects/overview');
    }

    public function projects_create() {
        return Inertia::render('apps/projects/create');
    }

    public function tasks_details() {
        return Inertia::render('apps/tasks/details');
    }

    public function tasks_kanban() {
        return Inertia::render('apps/tasks/kanban');
    }

    public function tasks_list_view() {
        return Inertia::render('apps/tasks/list-view');
    }

    public function tickets_details() {
        return Inertia::render('apps/tickets/details');
    }

    public function tickets_list() {
        return Inertia::render('apps/tickets/list');
    }

    

    public function ecommerce_customers() {
        return Inertia::render('apps/ecommerce/customers');
    }

    public function ecommerce_products() {
        return Inertia::render('apps/ecommerce/products');
    }

    public function ecommerce_product_details() {
        return Inertia::render('apps/ecommerce/product-details');
    }

    public function ecommerce_orders() {
        return Inertia::render('apps/ecommerce/orders');
    }

    public function ecommerce_order_details() {
        return Inertia::render('apps/ecommerce/order-details');
    }

    public function ecommerce_add_product() {
        return Inertia::render('apps/ecommerce/add-product');
    }

    public function ecommerce_shopping_cart() {
        return Inertia::render('apps/ecommerce/shopping-cart');
    }

    public function ecommerce_checkout() {
        return Inertia::render('apps/ecommerce/checkout');
    }

    public function ecommerce_sellers() {
        return Inertia::render('apps/ecommerce/sellers');
    }

    public function ecommerce_seller_details() {
        return Inertia::render('apps/ecommerce/seller-details');
    }

    public function crypto_buy_sell() {
        return Inertia::render('apps/crypto/buy-sell');
    }

    public function crypto_kyc() {
        return Inertia::render('apps/crypto/kyc');
    }

    public function crypto_ico() {
        return Inertia::render('apps/crypto/ico');
    }

    public function crypto_orders() {
        return Inertia::render('apps/crypto/orders');
    }

    public function crypto_wallet() {
        return Inertia::render('apps/crypto/wallet');
    }

    public function crypto_transactions() {
        return Inertia::render('apps/crypto/transactions');
    }

    public function invoices_detail() {
        return Inertia::render('apps/invoices/details');
    }

    public function invoices_list() {
        return Inertia::render('apps/invoices/list');
    }

    public function invoices_create() {
        return Inertia::render('apps/invoices/create');
    }

    public function jobs_application() {
        return Inertia::render('apps/jobs/application');
    }

    public function jobs_candidate_grid() {
        return Inertia::render('apps/jobs/candidate-grid');
    }

    public function jobs_candidate_lists() {
        return Inertia::render('apps/jobs/candidate-lists');
    }

    public function jobs_categories() {
        return Inertia::render('apps/jobs/categories');
    }

    public function jobs_companies_list() {
        return Inertia::render('apps/jobs/companies-list');
    }

    public function jobs_details() {
        return Inertia::render('apps/jobs/details');
    }

    public function jobs_grid() {
        return Inertia::render('apps/jobs/grid-lists');
    }

    public function jobs_lists() {
        return Inertia::render('apps/jobs/lists');
    }

    public function jobs_new() {
        return Inertia::render('apps/jobs/new');
    }

    public function jobs_statistics() {
        return Inertia::render('apps/jobs/statistics');
    }

    public function apps_api_key() {
        return Inertia::render('apps/apikey');
    }

    public function ui_alerts() {
        return Inertia::render('ui/alerts');
    }

    public function ui_buttons() {
        return Inertia::render('ui/buttons');
    }

    public function ui_cards() {
        return Inertia::render('ui/cards');
    }

    public function ui_carousel() {
        return Inertia::render('ui/carousel');
    }

    public function ui_dropdowns() {
        return Inertia::render('ui/dropdowns');
    }

    public function ui_grid() {
        return Inertia::render('ui/grid');
    }

    public function ui_images() {
        return Inertia::render('ui/images');
    }

    public function ui_modals() {
        return Inertia::render('ui/modals');
    }

    public function ui_offcanvas() {
        return Inertia::render('ui/offcanvas');
    }

    public function ui_progress() {
        return Inertia::render('ui/progress');
    }

    public function ui_placeholders() {
        return Inertia::render('ui/placeholders');
    }

    public function ui_accordions() {
        return Inertia::render('ui/accordions');
    }

    public function ui_tabs() {
        return Inertia::render('ui/tabs');
    }

    public function ui_typography() {
        return Inertia::render('ui/typography');
    }

    public function ui_embed_video() {
        return Inertia::render('ui/embed-video');
    }

    public function ui_ribbons() {
        return Inertia::render('ui/ribbons');
    }

    public function ui_lists() {
        return Inertia::render('ui/lists');
    }

    public function ui_links() {
        return Inertia::render('ui/links');
    }

    public function ui_utilities() {
        return Inertia::render('ui/utilities');
    }

    public function ui_notifications() {
        return Inertia::render('ui/notifications');
    }

    public function ui_general() {
        return Inertia::render('ui/general');
    }

    public function ui_colors() {
        return Inertia::render('ui/colors');
    }

    public function ui_badges() {
        return Inertia::render('ui/badges');
    }

    public function ui_media() {
        return Inertia::render('ui/media');
    }

    public function widgets() {
        return Inertia::render('widgets/index');
    }

    public function icons_boxicons() {
        return Inertia::render('icons/boxicons');
    }

    public function icons_materialdesign() {
        return Inertia::render('icons/materialdesign');
    }

    public function icons_feather() {
        return Inertia::render('icons/feather');
    }

    public function icons_lineawesome() {
        return Inertia::render('icons/lineawesome');
    }

    public function icons_remix() {
        return Inertia::render('icons/remix');
    }

    public function icons_crypto() {
        return Inertia::render('icons/crypto');
    }

    public function tables_basic() {
        return Inertia::render('tables/basic');
    }

    public function tables_gridjs() {
        return Inertia::render('tables/grid/index');
    }

    public function form_advanced() {
        return Inertia::render('forms/advanced');
    }

    public function form_elements() {
        return Inertia::render('forms/elements');
    }

    public function form_layouts() {
        return Inertia::render('forms/layouts');
    }

    public function form_editors() {
        return Inertia::render('forms/editors');
    }

    public function form_file_uploads() {
        return Inertia::render('forms/file-uploads');
    }

    public function form_validation() {
        return Inertia::render('forms/validation');
    }

    public function form_wizard() {
        return Inertia::render('forms/wizard');
    }

    public function form_masks() {
        return Inertia::render('forms/masks');
    }

    public function form_pickers() {
        return Inertia::render('forms/pickers');
    }

    public function form_range_sliders() {
        return Inertia::render('forms/range-sliders');
    }

    public function form_select() {
        return Inertia::render('forms/select');
    }

    public function form_checkboxs_radios() {
        return Inertia::render('forms/checkboxs-radios');
    }

    public function landing() {
        return Inertia::render('landing/index');
    }

    public function nft_landing() {
        return Inertia::render('landing/nft-landing');
    }

    public function job_landing() {
        return Inertia::render('landing/job-landing');
    }

    public function pages_starter() {
        return Inertia::render('pages/starter');
    }

    public function pages_profile() {
        return Inertia::render('pages/profile/simple');
    }

    public function pages_profile_setting() {
        return Inertia::render('pages/profile/setting');
    }

    public function pages_maintenance() {
        return Inertia::render('pages/maintenance');
    }

    public function pages_coming_soon() {
        return Inertia::render('pages/coming-soon');
    }

    public function pages_timeline() {
        return Inertia::render('pages/timeline');
    }

    public function pages_faqs() {
        return Inertia::render('pages/faqs');
    }

    public function pages_pricing() {
        return Inertia::render('pages/pricing');
    }

    public function pages_team() {
        return Inertia::render('pages/team');
    }

    public function pages_search_results() {
        return Inertia::render('pages/search-results');
    }

    public function pages_sitemap() {
        return Inertia::render('pages/sitemap');
    }

    public function pages_privacy_policy() {
        return Inertia::render('pages/privacy-policy');
    }

    public function pages_term_conditions() {
        return Inertia::render('pages/term-conditions');
    }

    public function charts_chartjs() {
        return Inertia::render('charts/chartjs/index');
    }

    public function charts_echart() {
        return Inertia::render('charts/echart/index');
    }

    public function charts_apex_line() {
        return Inertia::render('charts/apex/line/index');
    }

    public function charts_apex_area() {
        return Inertia::render('charts/apex/area/index');
    }

    public function charts_apex_bar() {
        return Inertia::render('charts/apex/bar/index');
    }

    public function charts_apex_column() {
        return Inertia::render('charts/apex/column/index');
    }

    public function charts_apex_mixed() {
        return Inertia::render('charts/apex/mixed/index');
    }

    public function charts_apex_range() {
        return Inertia::render('charts/apex/rangearea/index');
    }

    public function charts_apex_funnel() {
        return Inertia::render('charts/apex/funnel/index');
    }

    public function charts_apex_candlestick() {
        return Inertia::render('charts/apex/candlestick/index');
    }

    public function charts_apex_boxplot() {
        return Inertia::render('charts/apex/boxplot/index');
    }

    public function charts_apex_bubble() {
        return Inertia::render('charts/apex/bubble/index');
    }

    public function charts_apex_scatter() {
        return Inertia::render('charts/apex/scatter/index');
    }

    public function charts_apex_heatmap() {
        return Inertia::render('charts/apex/heatmap/index');
    }

    public function charts_apex_treemap() {
        return Inertia::render('charts/apex/treemap/index');
    }

    public function charts_apex_pie() {
        return Inertia::render('charts/apex/pie/index');
    }

    public function charts_apex_radialbar() {
        return Inertia::render('charts/apex/radialbar/index');
    }

    public function charts_apex_radar() {
        return Inertia::render('charts/apex/radar/index');
    }

    public function charts_apex_polararea() {
        return Inertia::render('charts/apex/polararea/index');
    }

    public function advance_ui_animation() {
        return Inertia::render('advance-ui/animation');
    }

    public function advance_ui_highlight() {
        return Inertia::render('advance-ui/highlight');
    }

    public function advance_ui_scrollbar() {
        return Inertia::render('advance-ui/scrollbar');
    }

    public function advance_ui_scrollspy() {
        return Inertia::render('advance-ui/scrollspy');
    }

    public function advance_ui_sweetalerts() {
        return Inertia::render('advance-ui/sweetalerts');
    }

    public function advance_ui_swiper() {
        return Inertia::render('advance-ui/swiper');
    }

    public function auth_signin_basic() {
        return Inertia::render('auth-pages/signin/basic');
    }

    public function auth_signin_cover() {
        return Inertia::render('auth-pages/signin/cover');
    }

    public function auth_signup_basic() {
        return Inertia::render('auth-pages/signup/basic');
    }

    public function auth_signup_cover() {
        return Inertia::render('auth-pages/signup/cover');
    }

    public function auth_reset_pwd_basic() {
        return Inertia::render('auth-pages/reset/basic');
    }

    public function auth_reset_pwd_cover() {
        return Inertia::render('auth-pages/reset/cover');
    }

    public function auth_create_pwd_basic() {
        return Inertia::render('auth-pages/create/basic');
    }

    public function auth_create_pwd_cover() {
        return Inertia::render('auth-pages/create/cover');
    }

    public function auth_lockscreen_basic() {
        return Inertia::render('auth-pages/lockscreen/basic');
    }

    public function auth_lockscreen_cover() {
        return Inertia::render('auth-pages/lockscreen/cover');
    }

    public function auth_twostep_basic() {
        return Inertia::render('auth-pages/twostep/basic');
    }

    public function auth_twostep_cover() {
        return Inertia::render('auth-pages/twostep/cover');
    }

    public function auth_404() {
        return Inertia::render('auth-pages/errors/404');
    }

    public function auth_500() {
        return Inertia::render('auth-pages/errors/500');
    }

    public function auth_404_basic() {
        return Inertia::render('auth-pages/errors/404-basic');
    }

    public function auth_404_cover() {
        return Inertia::render('auth-pages/errors/404-cover');
    }

    public function auth_ofline() {
        return Inertia::render('auth-pages/errors/ofline');
    }

    public function auth_logout_basic() {
        return Inertia::render('auth-pages/logout/basic');
    }

    public function auth_logout_cover() {
        return Inertia::render('auth-pages/logout/cover');
    }

    public function auth_success_msg_basic() {
        return Inertia::render('auth-pages/success-msg/basic');
    }

    public function auth_success_msg_cover() {
        return Inertia::render('auth-pages/success-msg/cover');
    }

    public function maps_amcharts() {
        return Inertia::render('maps/amcharts/index');
    }

    public function maps_google() {
        return Inertia::render('maps/google');
    }

    public function maps_leaflet() {
        return Inertia::render('maps/leaflet/index');
    }

    public function hr_employees() {
        return Inertia::render('apps/hr/employee');
    }

    public function hr_attendance() {
        return Inertia::render('apps/hr/attendance');
    }

    public function hr_leaves() {
        return Inertia::render('apps/hr/leave');
    }

    public function hr_leave_allocation() {
        return Inertia::render('apps/hr/leave-allocation');
    }

    public function hr_compensatory_leave_request() {
        return Inertia::render('apps/hr/compensatory-leave-request');
    }

    public function hr_leave_type() {
        return Inertia::render('apps/hr/leave-type');
    }

    public function hr_holidays() {
        return Inertia::render('apps/hr/holidays');
    }

    public function hr_employee_profile(Request $request, $id) {
        return Inertia::render('apps/hr/employee-profile', [
            'empid' => $id,
        ]);
    }

    public function hr_department() {
        return Inertia::render('apps/hr/department');
    }

    public function hr_designation() {
        return Inertia::render('apps/hr/designation');
    }

    public function hr_employment_type() {
        return Inertia::render('apps/hr/employment-type');
    }

    public function company_branches() {
        return Inertia::render('company/branches');
    }

    public function hr_employee_grades() {
        return Inertia::render('apps/hr/employee-grades');
    }

    // Inventory
    public function inventory_stock_settings() {
        return Inertia::render('apps/inventory/stock-settings');
    }
}
