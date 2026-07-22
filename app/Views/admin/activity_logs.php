<div id="page-wrapper">
    <div class="col-md-12 graphs">
        <div class="xs">
            <h3>Activity Logs</h3>
            <div class="clearfix"></div>

            <div class="bs-example4" data-example-id="contextual-table">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Sr.No.</th>
                                <?php foreach (array_keys($logs[0] ?? []) as $column) : ?>
                                    <th><?= esc(ucwords(str_replace('_', ' ', $column))); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($logs)) : ?>
                                <?php foreach ($logs as $row) : ?>
                                    <?php $count = ($count ?? 0) + 1; ?>
                                    <tr>
                                        <td><?= $count; ?></td>
                                        <?php foreach ($row as $value) : ?>
                                            <td><?= esc((string) $value); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="10" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <p class="pagination"><?= $links ?? ''; ?></p>
            </div>
        </div>
    </div>
</div>
