<div class="table-responsive">
    <table class="table table-striped table-hover">
        <?php if (isset($flag) && $flag == 'load-prev') { ?>
            <tr>
                <th scope="row" align="left" width="15%"><a href="javascript:void(0);" title="Service" class="tag-header-black tag-header-cursor-default">Service</a></th>
                <td headers="service" align="left"><?php echo $services; ?></td>
            </tr>
        <?php } else { ?>
            <tr>
                <th scope="row" align="left" width="15%"><a href="javascript:void(0);" title="Comments" class="tag-header-black tag-header-cursor-default">Comments</a></th>
                <td headers="comment" align="left"><?php echo $comment; ?></td>
            </tr>

            <tr>
                <th scope="row" align="left" width="15%"><a href="javascript:void(0);" title="Service" class="tag-header-black tag-header-cursor-default">Service</a></th>
                <td headers="services" align="left"><?php echo $services; ?></td>
            </tr>
            <tr>
                <th scope="row" align="left" width="15%"><a href="javascript:void(0);" title="Completed Date" class="tag-header-black tag-header-cursor-default">Completed Date</a></th>
                <td headers="completed_date" align="left"><?php echo $completed_date; ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
