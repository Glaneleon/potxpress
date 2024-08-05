<?php require_once "./adminconfig/adminhead.php"; ?>

<div>
    <h2>Delivery Riders</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
        Add Rider
    </button>
    <button id="reloadDataTable" class="btn btn-secondary" title="Manually reload the table."><i class="fa-solid fa-arrows-rotate"></i></button>

    <table id="ridersTable" class="table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>Rider ID</th>
                <th>Full Name</th>
                <th>Contact Number</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>

</div>

<!-- Add Rider Modal -->
<div class="modal modal-lg fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="contactModalLabel">New Rider</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRider">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="contact_number" name="contact_number" pattern="[0-9]{11}" required>
                    </div>
                    <div class="mb-3">
                        <label for="license_number" class="form-label">License Number</label>
                        <input type="text" class="form-control" id="license_number" name="license_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="license_expiry_date" class="form-label">License Expiry Date</label>
                        <input type="date" class="form-control" id="license_expiry_date" name="license_expiry_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_type" class="form-label">Vehicle Type</label>
                        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_plate_number" class="form-label">Vehicle Plate Number</label>
                        <input type="text" class="form-control" id="vehicle_plate_number" name="vehicle_plate_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option selected value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_hired" class="form-label">Date Hired</label>
                        <input type="date" class="form-control" id="date_hired" name="date_hired" required>
                    </div>
                    <div class="mb-3">
                        <label for="bank_account_number" class="form-label">Bank Account Number</label>
                        <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Bank Account Name</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="emergency_contact_number" class="form-label">Emergency Contact Number</label>
                        <input type="tel" class="form-control" id="emergency_contact_number" name="emergency_contact_number" pattern="[0-9]{11}" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="addRider" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Rider Modal -->
<div class="modal fade" id="editRiderModal" tabindex="-1" aria-labelledby="editRiderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRiderModalLabel">Edit Rider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="editRiderForm">
                <input hidden type="text" name="riderId" id="riderId">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name">
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="contact_number" name="contact_number"  pattern="[0-9]{11}" required>
                    </div>
                    <div class="mb-3">
                        <label for="license_number" class="form-label">License Number</label>
                        <input type="text" class="form-control" id="license_number" name="license_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="license_expiry_date" class="form-label">License Expiry Date</label>
                        <input type="date" class="form-control" id="license_expiry_date" name="license_expiry_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_type" class="form-label">Vehicle Type</label>
                        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" required>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_plate_number" class="form-label">Vehicle Plate Number</label>
                        <input type="text" class="form-control" id="vehicle_plate_number" name="vehicle_plate_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option selected value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_hired" class="form-label">Date Hired</label>
                        <input type="date" class="form-control" id="date_hired" name="date_hired" required>
                    </div>
                    <div class="mb-3">
                        <label for="bank_account_number" class="form-label">Bank Account Number</label>
                        <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Bank Account Name</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="emergency_contact_number" class="form-label">Emergency Contact Number</label>
                        <input type="tel" class="form-control" id="emergency_contact_number" name="emergency_contact_number" pattern="[0-9]{11}" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveEdit">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Rider Modal -->
<div class="modal fade" id="deleteRiderModal" tabindex="-1" aria-labelledby="deleteRiderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRiderModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this rider?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="deleteRider">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php require_once "./adminconfig/adminscript.php"; ?>
