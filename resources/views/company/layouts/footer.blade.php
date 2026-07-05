<!-- /.content-wrapper -->
<footer class="main-footer d-flex justify-content-between align-items-center">

  <div>
    <strong>
      Copyright &copy; 2025

      <a href="{{ route('dashboard') }}" style="color:#fff;">

        {{ $settings->company_name ?? 'Demo' }}
        | {{ ucfirst(Auth::user()->name) }}

      </a>.
    </strong>

    All rights reserved.
  </div>

  <div class="text-right">

    <span class="mr-3">
      Version :
      <strong style="color:#fff;">
        v2.1.3
      </strong>
    </span>

    Powered By

    <a href="https://onistech.com" target="_blank" style="color:#fff;font-weight:bold;">

      Onistech Info Systems

    </a>
  </div>

</footer>
<div class="modal fade" id="addCountryModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
  data-keyboard="false">
  <div class="modal-dialog" role="document">
    <form id="addCountryForm" action="javascript:void(0);" autocomplete="off">
      @csrf
      <div class="modal-content">
        <div class="modal-header"
          style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
          <h5 class="modal-title text-white">Add Country</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Country Name</label>
            <input type="text" name="name" placeholder="Enter Country Name " class="form-control" required>
          </div>
          <div class="form-group">
            <label>Code</label>
            <input type="text" name="code" maxlength="5" placeholder="IN" class="form-control">
          </div>
          <div class="form-group">
            <label>Phone Code</label>
            <input type="text" name="phonecode" maxlength="5" placeholder="91" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Country</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="addStateModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
  data-keyboard="false">
  <div class="modal-dialog" role="document">
    <form id="addStateForm" autocomplete="off">
      @csrf
      <div class="modal-content">
        <div class="modal-header"
          style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
          <h5 class="modal-title text-white">Add State</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Country</label>
            <select name="country_id" id="state_country" class="form-control select2" required>
              <option value="">Select Country</option>
              @foreach($countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>State Name</label>
            <input type="text" name="name" placeholder="Enter State Name " class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save State</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="addCityModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
  data-keyboard="false">
  <div class="modal-dialog" role="document">
    <form id="addCityForm" autocomplete="off">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white"
          style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
          <h5 class="modal-title">Add City</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Country</label>
            <select name="country_id" id="city_country" class="form-control select2" required>
              <option value="">Select Country</option>
              @foreach($countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>State</label>
            <select name="state_id" id="city_state" class="form-control select2" required>
              <option value="">Select State</option>
            </select>
          </div>
          <div class="form-group">
            <label>City Name</label>
            <input type="text" name="name" placeholder="Enter City Name " class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save City</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade" id="leadDetailsModal" tabindex="-1" aria-hidden="true" data-backdrop="static"
  data-keyboard="false">
  <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
    <div class="modal-content">

      <div class="modal-header bg-teal "
        style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
        <h5 class="modal-title">Lead Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div id="lead-details-content">
          <p class="text-center">Loading...</p>
        </div>

      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="addLeadModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-teal"
        style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">

        <h5 class="modal-title">Quick Add Lead</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="quickLeadForm" autocomplete="off">
        @csrf

        <div class="modal-body">
          <div class="row">

            <div class="col-md-6">
              <label>Customer Name *</label>
              <input type="text" name="customerName" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label>Mobile *</label>
              <input type="text" name="mobile" class="form-control" maxlength="10" required>
            </div>

            <div class="col-md-6 mt-2">
              <label>Email</label>
              <input type="email" name="email" class="form-control">
            </div>

            <div class="col-md-6 mt-2">
              <label>Purpose</label>
              <input type="text" name="purpose" class="form-control">
            </div>

            <div class="col-md-4 mt-2">
              <label>Country *</label>
              <select name="country" id="modal_country" class="form-control select2" required>
                <option value="">Select Country</option>
                @foreach($countries as $country)
                  <option value="{{ $country->id }}">
                    {{ $country->name }}
                  </option>
                @endforeach
              </select>

            </div>

            <div class="col-md-4 mt-2">
              <label>State *</label>
              <select name="state" id="modal_state" class="form-control select2" required></select>
            </div>

            <div class="col-md-4 mt-2">
              <label>City *</label>
              <select name="city" id="modal_city" class="form-control select2" required></select>
            </div>

            <div class="col-md-12 mt-2">
              <label>Remark</label>
              <textarea name="remark" class="form-control"></textarea>
            </div>
            <div class="col-md-12 mt-2">
              <label>Address</label>
              <textarea name="address" class="form-control"></textarea>
            </div>

            <div class="col-md-6 mt-2">
              <label>Reference</label>
              <input type="text" name="reference" class="form-control">
            </div>

            <!-- <div class="col-md-6 mt-2">
              <label>Message</label>
              <input type="text" name="message" class="form-control">
            </div> -->

          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            Save Lead
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="categoryModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="categoryModalTitle">Add Category</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="categoryForm" autocomplete="off">
        @csrf
        <input type="hidden" id="category_id">

        <div class="modal-body">
          <label>Category Name *</label>
          <input type="text" id="category_name" class="form-control" required>
        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="subcategoryModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="subcategoryModalTitle">Add Sub Category</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="subcategoryForm" autocomplete="off">
        @csrf
        <input type="hidden" id="subcategory_id">

        <div class="modal-body">

          {{-- CATEGORY --}}
          <label>Category *</label>
          <div class="d-flex align-items-start w-100">
            <select id="subcategory_category" class="form-control select2" data-placeholder="Search category..."
              required style="width:100%">
              <option value="">Select Category</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            </select>

            <button type="button" class="btn btn-outline-success" id="openCategoryFromSubcategory">
              Add
            </button>
          </div>


          {{-- SUB CATEGORY --}}
          <label class="mt-2">Sub Category Name *</label>
          <input type="text" id="subcategory_name" class="form-control" required>

        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="unitModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="background: linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72);">
        <h5 class="modal-title text-white" id="unitModalTitle">Add Unit</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="unitForm" autocomplete="off">
        @csrf
        <input type="hidden" id="unit_id">

        <div class="modal-body">
          <div class="form-group">
            <label>Unit Name *</label>
            <input type="text" name="name" id="unit_name" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-success" id="unitSaveBtn">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="conditionModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="conditionModalTitle">
          Add Condition
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          &times;
        </button>
      </div>

      <form id="conditionForm" autocomplete="off">
        @csrf
        <input type="hidden" id="condition_id">

        <div class="modal-body">
          <label>Condition Name <span class="text-danger">*</span></label>
          <input type="text" id="condition_name" class="form-control" placeholder="Enter condition name" required>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Save
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="brandModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="background: linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72);">
        <h5 class="modal-title text-white" id="brandModalTitle">
          Add Brand
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          &times;
        </button>
      </div>

      <form id="brandForm" autocomplete="off">
        @csrf
        <input type="hidden" id="brand_id">

        <div class="modal-body">
          <div class="form-group">
            <label>Brand Name <span class="text-danger">*</span></label>
            <input type="text" id="brand_name" class="form-control" placeholder="Enter brand name" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Save
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="itemModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="itemModalTitle">Add Item</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="itemForm" autocomplete="off">
        @csrf
        <input type="hidden" id="item_id">

        <div class="modal-body">

          {{-- ITEM NAME --}}
          <div class="form-group">
            <label>Item Name *</label>
            <input type="text" id="item_name" class="form-control" placeholder="Enter Item name" required>
          </div>

          {{-- CATEGORY --}}
          <div class="form-group">
            <label>Category *</label>
            <div class="d-flex">
              <select id="item_category" class="form-control select2" required>
                <option value="">Select Category</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
              <button type="button" class="btn btn-outline-success" onclick="openCreateCategory()">Add</button>
            </div>
          </div>

          {{-- SUBCATEGORY --}}
          <div class="form-group">
            <label>Sub Category *</label>
            <div class="d-flex">
              <select id="item_subcategory" class="form-control select2" required>
                <option value="">Select Subcategory</option>
              </select>
              <button type="button" class="btn btn-outline-success" onclick="openCreateSubcategory()">Add</button>
            </div>
          </div>

          {{-- LOW STOCK --}}
          <div class="form-group">
            <label>Low Stock Level</label>
            <input type="number" id="low_stock_level" placeholder="Enter stock level" class="form-control">
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Save Item
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="departmentModal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="departmentModalTitle">
          Add Department
        </h5>
        <button class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="departmentForm" autocomplete="off">
        @csrf
        <input type="hidden" id="department_id">

        <div class="modal-body">
          <label>Department Name *</label>
          <input type="text" id="department_name" class="form-control" required>
        </div>

        <div class="modal-footer">
          <button class="btn btn-success">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>

<div class="modal fade" id="projectModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="projectModalTitle">
          Add Project
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      {{-- FORM --}}
      <form id="projectForm" autocomplete="off">
        @csrf
        <input type="hidden" id="project_id" name="project_id">
        <div class="modal-body">

          {{-- PROJECT NAME --}}
          <div class="form-group">
            <label>Project Name *</label>
            <input type="text" id="project_name" name="name" class="form-control" placeholder="Enter project name"
              required>
          </div>

          {{-- PROJECT CODE --}}
          <div class="form-group">
            <label>Project Code *</label>
            <input type="text" id="project_code" name="code" class="form-control" placeholder="Enter project code"
              required>
          </div>

          {{-- DESCRIPTION --}}
          <div class="form-group">
            <label>Description</label>
            <textarea id="project_desc" name="description" class="form-control" rows="2"
              placeholder="Enter project description"></textarea>
          </div>

          {{-- DATES --}}
          <div class="row">
            <div class="col-md-6 form-group">
              <label>Start Date *</label>
              <div class="input-group" style="cursor:pointer;">
                <input type="text" id="project_start" name="start_date" class="form-control" placeholder="DD/MM/YYYY">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
              </div>
            </div>

            <div class="col-md-6 form-group">
              <label>Estimated End Date *</label>
              <div class="input-group" style="cursor:pointer;">
                <input type="text" id="project_end" name="end_date" class="form-control" placeholder="DD/MM/YYYY">
                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
              </div>
            </div>
          </div>

        </div>

        {{-- FOOTER --}}
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Save Project
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="supplierModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      {{-- HEADER --}}
      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="supplierModalTitle">
          Add Supplier
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      {{-- FORM --}}
      <form id="supplierForm" autocomplete="off">
        @csrf
        <input type="hidden" id="supplier_id" name="supplier_id">

        <div class="modal-body">

          {{-- SUPPLIER NAME --}}
          <div class="form-group">
            <label>Supplier Name *</label>
            <input type="text" id="sup_name" name="name" class="form-control" placeholder="Enter supplier name"
              required>
          </div>

          {{-- ADDRESS --}}
          <div class="form-group">
            <label>Address *</label>
            <textarea id="sup_address" name="address" class="form-control" rows="2" placeholder="Enter supplier address"
              required></textarea>
          </div>

          {{-- STATE & CITY --}}
          <div class="row">

            {{-- COUNTRY --}}
            <div class="col-md-4 form-group">
              <label>Country *</label>
              <div class="input-group">
                <select id="sup_country" name="country_id" class="form-control select2" required>
                  <option value="">Select Country</option>
                  @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                  @endforeach
                </select>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-success" onclick="openCountryModal()">+</button>
                </div>
              </div>
            </div>

            {{-- STATE --}}
            <div class="col-md-4 form-group">
              <label>State *</label>
              <div class="input-group">
                <select id="sup_state" name="state_id" class="form-control select2" required>
                  <option value="">Select State</option>
                </select>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-success" onclick="openStateModal()">+</button>
                </div>
              </div>
            </div>

            {{-- CITY --}}
            <div class="col-md-4 form-group">
              <label>City *</label>
              <div class="input-group">
                <select id="sup_city" name="city_id" class="form-control select2" required>
                  <option value="">Select City</option>
                </select>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-success" onclick="openCityModal()">+</button>
                </div>
              </div>
            </div>

          </div>

          {{-- TIN --}}
          <div class="form-group">
            <label>TIN No.</label>
            <input type="text" id="sup_tin" name="tin_no" class="form-control" placeholder="Enter TIN number">
          </div>

          {{-- EMAIL --}}
          <div class="form-group">
            <label>Email *</label>
            <input type="email" id="sup_email" name="email" class="form-control" placeholder="Enter email" required>
          </div>

          {{-- MOBILE --}}
          <div class="form-group">
            <label>Mobile *</label>
            <input type="text" id="sup_mobile" name="mobile" class="form-control" maxlength="10"
              placeholder="10 digit mobile number" required>
          </div>

        </div>

        {{-- FOOTER --}}
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Save Supplier
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="customer360Modal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header text-white"
        style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
        <h5 class="modal-title" id="customerNameTitle">Customer Overview</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="customerTabs">
          <li class="nav-item">
            <a class="nav-link active" data-tab="leads">Leads</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="quotations">Quotations</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="orders">Orders</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="payments">Payments</a>
          </li>
        </ul>

        <!-- Content -->
        <div id="customerTabContent" class="mt-3">
          <div class="text-center py-5">
            <i class="fa fa-spinner fa-spin"></i> Loading...
          </div>
        </div>

      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="descriptionModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header" style="background: linear-gradient(to bottom, #081a2d 0%, #0f3057 60%, #1b4f72 100%);">
        <h5 class="modal-title text-white">Edit Item Description</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">

        <!-- ENGLISH -->
        <div class="mb-3">
          <label><b>Description (English)</b></label>
          <textarea id="modalDescriptionEn" class="form-control summernote" rows="4"></textarea>
        </div>

        <!-- HINDI -->
        <div>
          <div class="d-flex justify-content-between align-items-center">
            <label><b>Description (Hindi)</b></label>
          </div>

          <textarea id="modalDescriptionHi" class="form-control summernote" rows="4"></textarea>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Cancel
        </button>
        <button type="button" class="btn btn-success" id="saveDescription">
          Save
        </button>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="specModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Specification</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <textarea id="modalSpec" class="form-control summernote"></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="saveSpec">
          Save
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Close
        </button>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="currencyModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5>Enter Conversion Rate</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="text" id="modal_rate" class="form-control modal_rate" placeholder="Enter rate">
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="save_rate">Save</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="priorityModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="priorityModalTitle">Add Priority</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="priorityForm">
        @csrf
        <input type="hidden" id="priority_id">

        <div class="modal-body">
          <div class="form-group">
            <label>Name</label>
            <input type="text" id="priority_name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Level</label>
            <input type="number" id="priority_level" class="form-control" min="1" max="10" step="1" required
              oninput="if (this.value > 10) this.value = 10; if (this.value < 0) this.value = 0;">
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-success" id="prioritySaveBtn">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="shiftModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="shiftModalTitle">Add Shift</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="shiftForm">
        @csrf
        <input type="hidden" id="shift_id">

        <div class="modal-body">

          <div class="form-group">
            <label>Shift Name</label>
            <input type="text" id="shift_name" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Start Time</label>
            <input type="time" id="shift_start" class="form-control">
          </div>

          <div class="form-group">
            <label>End Time</label>
            <input type="time" id="shift_end" class="form-control">
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-success" id="shiftSaveBtn">Save</button>
        </div>

      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="specsModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white" id="specModalTitle">Add Specification</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="specForm">
        @csrf
        <input type="hidden" id="spec_id">

        <div class="modal-body">

          <div class="form-group">
            <label>Specification Name</label>
            <input type="text" id="spec_name" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-success" id="specSaveBtn">Save</button>
        </div>

      </form>

    </div>
  </div>
</div>
<div class="modal fade" id="bomItemModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl  modal-dialog-scrollable"">
    <div class=" modal-content">

    <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
      <h6 class="modal-title text-white">
        BOM - <span id="bom_item_title"></span>
      </h6>
      <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">

      <input type="hidden" id="modal_order_item_id">

      <!-- PARTS WRAPPER -->
      <div id="bomPartsWrapper"></div>

      <!-- ADD PART -->
      <button type="button" class="btn btn-success btn-sm mt-2" onclick="addBomPart()">
        <i class="fa fa-plus"></i> Add Part
      </button>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" onclick="saveBomItems()">Save</button>
      </div>

    </div>
  </div>
</div>
</div>
<div class="modal fade" id="bomDetailsModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5 class="modal-title text-white">BOM Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body" id="bomDetailsContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin"></i> Loading...
        </div>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="recipePickerModal" tabindex="-1" data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header text-white" style="background:linear-gradient(to bottom,#081a2d,#0f3057,#1b4f72)">
        <h5>Select Recipe</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">

        <select id="recipe_picker" class="form-control">

        </select>

      </div>

      <div class="modal-footer">

        <button type="button" id="loadRecipeBtn" class="btn btn-success">

          Load Recipe

        </button>

      </div>

    </div>
  </div>
</div>