$ffstatus = FFEntiyDispatch::where('type', 'job_number')->where('status', 1)->where('companyId',$companyId);
        $ffstatus->where(function ($query) use ($deptId) {
            $query->whereRaw('"data" #>> \'{dept_id}\'=\'' . $deptId . '\'');
        });



select * from "ff_entiy_dispatch" where "type" = 'job_number' and "status" = '1' and "companyId" = '1274' and ("data" #>> '{dept_id
}'='5987') limit 1

data字段存储 {"id": "132", "status": 0, "dept_id": "433"}

 应用 select  content from  "public"."pe_growup_log" WHERE "content" #>> '{type}'='2' limit 12;
content 字段存储 {"desc": "转正申请", "type": 2, "status": 0, "approval_id": 51910, "status_text": "审批中"}



MsMpEng

dwm




   $builder=OaPositionRelation::where('companyId',$companyId);
   
//          $builder->whereRaw('("level_id" @> \'['. $id .']\')');
            $builder->whereRaw('("level_id" @> \'["'. $id .'"]\')');      

        $arr=$builder->get()->pluck('position_id')->toArray();



git log --author= wangyangyang  --no-decorate --name-only --after={2022-10-30} --no-merges --format="" | sort | uniq > 133019.txt

