<div class="tab-pane fade" id="riders" role="tabpanel" aria-labelledby="riders-tab">
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
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="contactModalLabel">New Rider</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="contactNumber" name="contactNumber" pattern="[0-9]{11}" required placeholder="Enter 11-digit mobile number. Example: 09XXXXXXXXX">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="contactForm" class="btn btn-primary">Save</button>
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
                    <input type="hidden" id="riderId" name="riderId">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="contactNumber" name="contactNumber" pattern="[0-9]{11}" required>
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