<div class="row">

    <div class="col-12">

        <div class="card card-info card-outline horometroCaptura">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 form-group">
                        <label for="fecha">Fecha:</label>
                        <input type="month" name="mes" id="mes" value="<?php $fecha_actual = date("Y-m"); echo $fecha_actual;?>" class="form-control form-control-sm">

                    </div>

                </div> <!-- <div class="row"> -->

                <div class="row">
                    <div class="col-12">
                        <div id="calendar"></div>

                    </div>
                </div>

            </div> <!-- <div class="card-body"> -->

        </div><!-- <div class="card card-info"> -->

    </div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->