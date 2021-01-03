            if bitrate == 24 then 
                ignore(output_stereo(%fdkaac(bitrate = 24, aot="mpeg4_he_aac_v2", afterburner=false, sbr_mode=true), !source))
            elsif bitrate == 32 then
                ignore(output_stereo(%fdkaac(bitrate = 32, aot="mpeg4_he_aac_v2", afterburner=false, sbr_mode=true), !source))
            elsif bitrate == 48 then
                ignore(output_stereo(%fdkaac(bitrate = 48, aot="mpeg4_he_aac_v2", afterburner=false, sbr_mode=true), !source))
            elsif bitrate == 64 then
                ignore(output_stereo(%fdkaac(bitrate = 64, aot="mpeg4_he_aac_v2", afterburner=false, sbr_mode=true), !source))
            elsif bitrate == 96 then
                ignore(output_stereo(%fdkaac(bitrate = 96, aot="mpeg4_aac_lc", afterburner=false, sbr_mode=true), !source))
            elsif bitrate == 128 then
                ignore(output_stereo(%fdkaac(bitrate = 128, aot="mpeg4_aac_lc", afterburner=false, sbr_mode=true), !source))
            elsif bitrate == 160 then
                ignore(output_stereo(%fdkaac(bitrate = 160, aot="mpeg4_aac_lc", afterburner=true, sbr_mode=true), !source))
            elsif bitrate == 192 then
                ignore(output_stereo(%fdkaac(bitrate = 192, aot="mpeg4_aac_lc", afterburner=true, sbr_mode=true), !source))
            elsif bitrate == 224 then
                ignore(output_stereo(%fdkaac(bitrate = 224, aot="mpeg4_aac_lc", afterburner=true, sbr_mode=true), !source))
            elsif bitrate == 256 then
                ignore(output_stereo(%fdkaac(bitrate = 256, aot="mpeg4_aac_lc", afterburner=true, sbr_mode=true), !source))
            elsif bitrate == 320 then
                ignore(output_stereo(%fdkaac(bitrate = 320, aot="mpeg4_aac_lc", afterburner=true, sbr_mode=true), !source))
            end

